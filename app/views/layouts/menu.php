<?php

use App\Core\Database;
use App\Models\Read;

$db = new Database();
$pdo = $db->getConnection();

$read = new Read();
$usuario = $_SESSION['user_id'];


$current_url = $_SERVER['REQUEST_URI'];
?>

    <div id="sidebar" class="bg-dark">
        <div class="logo">
            <a href="<?= BASE_URL ?>"><img src="<?= BASE_IMG ?>/logo.png" width="50" height="auto" alt="Logo"></a>

        </div>

        <ul class="nav flex-column px-2">
            <li class="nav-item">
                <a href="<?= BASE_URL ?>" class="nav-link nav-toggle">Home</a>
            </li>

            <!-- FORNECEDOR -->
            <li class="nav-item">
                <div class="nav-link nav-toggle <?= strpos($current_url, '/fornecedor') !== false ? 'active' : '' ?>" onclick="toggleSubmenu('fornecedorMenu', this)">
                    <span>Fornecedor</span>
                    <span class="arrow-down"><i class="fa-solid fa-angles-down"></i></span>
                </div>
                <ul id="fornecedorMenu" class="list-unstyled submenu <?= strpos($current_url, '/fornecedor') !== false ? 'show' : '' ?>">
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>/fornecedor" class="nav-link"><i class="fa-solid fa-angles-right"></i>&nbsp;Cadastrar</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>/fornecedor/listar" class="nav-link"><i class="fa-solid fa-angles-right"></i>&nbsp;Listar</a>
                    </li>
                </ul>
            </li>

            <!-- COMPRAS -->
            <li class="nav-item">
                <div class="nav-link nav-toggle <?= strpos($current_url, '/compras') !== false ? 'active' : '' ?>" onclick="toggleSubmenu('comprasMenu', this)">
                    <span>Compras</span>
                    <span class="arrow-down"><i class="fa-solid fa-angles-down"></i></span>
                </div>

                <ul id="comprasMenu" class="list-unstyled submenu <?= strpos($current_url, '/compras') !== false ? 'show' : '' ?>">
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>/compras" class="nav-link"><i class="fa-solid fa-angles-right"></i>&nbsp;Cadastrar</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>/compras/historico" class="nav-link"><i class="fa-solid fa-angles-right"></i>&nbsp;Histórico</a>
                    </li>
                </ul>
            </li>

            <!-- ESTOQUE -->
            <li class="nav-item">
                <div class="nav-link nav-toggle <?= strpos($current_url, '/produtos') !== false ? 'active' : '' ?>" onclick="toggleSubmenu('estoqueSubmenu', this)">
                    <span>Estoque</span>
                    <span class="arrow-down"><i class="fa-solid fa-angles-down"></i></span>
                </div>

                <ul id="estoqueSubmenu" class="list-unstyled submenu <?= strpos($current_url, '/produtos') !== false ? 'show' : '' ?>">
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>/produtos/produtos" class="nav-link"><i class="fa-solid fa-angles-right"></i>&nbsp;Produtos</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>/produtos/entradas" class="nav-link"><i class="fa-solid fa-angles-right"></i>&nbsp;Entradas</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>/produtos/saida" class="nav-link"><i class="fa-solid fa-angles-right"></i>&nbsp;Saídas</a>
                    </li>
                </ul>
            </li>

            <hr>

        <?php if ($_SESSION['user_function_id'] <= 2): ?>
        <li class="nav-item"><a href="<?= BASE_URL . '/usuarios' ?>" class="nav-link">Usuários</a></li>
        <?php endif; ?>

            <?php if ($_SESSION['user_function_id'] <= 7): ?>
                <li class="nav-item"><a href="<?= BASE_URL . '/funcionarios' ?>" class="nav-link">Funcionários</a></li>
                <li class="nav-item"><a href="<?= BASE_URL . '/usuarios/perfil' ?>" class="nav-link">Meu Perfil</a></li>
            <?php else: ?>
                <li class="nav-item"><a href="<?= BASE_URL . '/usuarios/perfil' ?>" class="nav-link">Meu Perfil</a></li>
                <li class="nav-item"><a href="<?= BASE_URL . '/funcionarios' ?>" class="nav-link">Funcionarios</a></li>
            <?php endif; ?>


            <li class="nav-item"><a href="<?= BASE_URL . '/relatorios' ?>" class="nav-link">Relatórios</a></li>

            <?php if ($_SESSION['user_function_id'] <= 2): ?>
                <li class="nav-item"><a href="<?= BASE_URL . '/configuracao' ?>" class="nav-link">Configurações</a></li>
            <?php endif; ?>

            <li class="nav-item"><a href="<?= BASE_URL ?>/logout" class="nav-link">Sair</a></li>
        </ul>

        
    </div>



<script src="<?= BASEJS ?>/menu.js"></script>