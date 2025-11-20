<?php

namespace App\Models;

use App\Helpers\PHPMailerHelper;

class EmailCompras
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function sendConfirmationToUser(string $email, string $userName, array $dadosCompra = []): bool
{
    // Verifica se o ID do fornecedor foi passado
    if (empty($dadosCompra['fornecedor_id'])) {
        throw new \InvalidArgumentException("ID do fornecedor não informado.");
    }

    // Buscar fornecedor
    $stmtFornecedor = $this->pdo->prepare("SELECT fornecedor_razao FROM fornecedor WHERE fornecedor_id = :id");
    $stmtFornecedor->bindValue(':id', $dadosCompra['fornecedor_id'], \PDO::PARAM_INT);
    $stmtFornecedor->execute();
    $fornecedor = $stmtFornecedor->fetch(\PDO::FETCH_ASSOC);

    // Buscar autor
    $stmtUsuario = $this->pdo->prepare("SELECT user_name FROM users WHERE user_id = :user_id");
    $stmtUsuario->bindValue(':user_id', $dadosCompra['cad_autor'], \PDO::PARAM_INT);
    $stmtUsuario->execute();
    $usuario = $stmtUsuario->fetch(\PDO::FETCH_ASSOC);

    $produtos = $dadosCompra['produto'] ?? [];
    $produto_ids = $dadosCompra['produto_id'] ?? [];
    $quantidades = $dadosCompra['quantidade'] ?? [];
    $precos = $dadosCompra['preco'] ?? [];
    $previsoes = $dadosCompra['previsao'] ?? [];
    $previsao = $dadosCompra['previsao'] ?? date('Y-m-d');

    $produtosHtml = '';

    // Montar HTML de produtos
    foreach ($produtos as $i => $produto) {
        $produto_id = $produto_ids[$i] ?? null;
        $quantidade = $quantidades[$i] ?? '0';
        $preco = $precos[$i] ?? '0,00';
        

        // Buscar estoque atual do produto
        $estoqueAtual = 0;
        if ($produto_id) {
            $stmtEstoque = $this->pdo->prepare("SELECT estoque FROM produtos WHERE produto_id = :id");
            $stmtEstoque->bindValue(':id', $produto_id, \PDO::PARAM_INT);
            $stmtEstoque->execute();
            $row = $stmtEstoque->fetch(\PDO::FETCH_ASSOC);
            $estoqueAtual = $row['estoque'] ?? 0;
        }

        $produtosHtml .= "
            <p>
                <strong>Produto:</strong> " . htmlspecialchars($produto) . "<br/>
                <strong>Quantidade:</strong> " . htmlspecialchars($quantidade) . "<br/>
                <strong>Estoque Atual:</strong> " . htmlspecialchars($estoqueAtual) . "<br/>
                <strong>Preço:</strong> R$ " . htmlspecialchars($preco) . "<br/>
                
            </p>
            <hr/>
        ";
    }

    // Corpo do e-mail
    $body = "
    <p><strong>Fornecedor:</strong> " . htmlspecialchars($fornecedor['fornecedor_razao']) . "</p>
    <hr/>
    $produtosHtml
    <p>Data e hora do registro: " . date('d/m/Y H:i:s') . "</p>
    <p><strong>Previsão de entrega:</strong> " . date('d/m/Y', strtotime($previsao)) . "</p>
    <p>Cadastrado por: " . htmlspecialchars($usuario['user_name']) . "</p>
    <br>
    <p>Atenciosamente,<br><strong>" . SITE_NAME . "</strong></p>
";


    $mailer = new PHPMailerHelper($this->pdo);
    $subject = 'Nova compra cadastrada - ' . SITE_NAME;

    return $mailer->send($email, $userName, $subject, $body);
}

}
