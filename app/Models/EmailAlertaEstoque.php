<?php

namespace App\Models;

use App\Helpers\PHPMailerHelper;

class EmailAlertaEstoque
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Envia e-mail de alerta de estoque mínimo
     *
     * @param string $email Destinatário
     * @param string $userName Nome do remetente
     * @param array $dadosSaida Dados do formulário de saída
     * @return bool
     */
    public function sendConfirmationToUser(string $email, string $userName, array $dadosSaida = []): bool
    {
        $produtos = $dadosSaida['produto'] ?? [];
        $produto_ids = $dadosSaida['produto_id'] ?? [];
        $quantidades = $dadosSaida['quantidade'] ?? [];
        $precos = $dadosSaida['preco'] ?? [];
        $alertas = $dadosSaida['alerta'] ?? [];

        $produtosHtml = '';

        foreach ($produtos as $i => $produto) {
            $produto_id = $produto_ids[$i] ?? null;
            $quantidade = $quantidades[$i] ?? '0';
            $preco = $precos[$i] ?? '0,00';
            $alerta = $alertas[$i] ?? 0;

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
                    <strong>Quantidade saída:</strong> " . htmlspecialchars($quantidade) . "<br/>
                    <strong>Estoque atual:</strong> " . htmlspecialchars($estoqueAtual) . "<br/>
                    <strong>Alerta mínimo:</strong> " . htmlspecialchars($alerta) . "<br/>
                    <strong>Preço:</strong> R$ " . htmlspecialchars($preco) . "
                </p>
                <hr/>
            ";
        }

        // Corpo do e-mail
        $body = "
            <h3>Alerta de estoque mínimo atingido</h3>
            $produtosHtml
            <p>Data e hora do registro: " . date('d/m/Y H:i:s') . "</p>
            <br>
            <p>Atenciosamente,<br><strong>" . SITE_NAME . "</strong></p>
        ";

        $mailer = new PHPMailerHelper($this->pdo);
        $subject = 'Alerta de estoque mínimo atingido - ' . SITE_NAME;

        return $mailer->send($email, $userName, $subject, $body);
    }
}
