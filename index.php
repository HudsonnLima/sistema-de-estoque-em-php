<?php
session_start();
setlocale(LC_ALL, 'pt_BR.UTF-8');
ob_start();

date_default_timezone_set('America/Sao_Paulo');


require_once 'app/config/config.php';
require_once __DIR__ . '/vendor/autoload.php';

// BASE_URL definida em config.php, exemplo: https://www.ultimosdanoite.com.br/admin
$basePath = parse_url(BASE_URL, PHP_URL_PATH) ?? '';
$basePath = rtrim($basePath, '/'); // /admin

// Pega a URI da requisição
$requestUri = $_SERVER['REQUEST_URI'];
// Pega só o caminho (exclui query string)
$requestPath = parse_url($requestUri, PHP_URL_PATH);

// Remove o subdiretório base (/admin)
if ($basePath !== '' && strpos($requestPath, $basePath) === 0) {
    $requestPath = substr($requestPath, strlen($basePath));
}
$requestPath = '/' . ltrim($requestPath, '/'); // Garante barra inicial

// Remove barra final, exceto se for raiz
if ($requestPath !== '/') {
    $requestPath = rtrim($requestPath, '/');
}

// Defina rotas públicas
$publicRoutes = ['/login', '/login/remember', '/login/newpass', '/login/recoverypass'];

// Redireciona para login se não estiver logado e não for rota pública
if (!in_array($requestPath, $publicRoutes) && !isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_LOGIN);
    exit;
}

// Rotas disponíveis
$routes = [
    '/login' => 'app/views/home/login/index.php',
    '/login/remember' => 'app/views/home/login/remember.php',
    '/login/newpass' => 'app/views/home/login/newpass.php',
    '/login/recoverypass' => 'app/views/home/login/recoverypass.php',

    '/logout' => 'app/views/home/logout.php',

    '/' => 'app/views/home/pages/home.php',
    '/home' => 'app/views/home/pages/home.php',

    '/compras' => 'app/views/home/pages/compras/index.php',
    '/compras/editar' => 'app/views/home/pages/compras/editar.php',
    '/compras/historico' => 'app/views/home/pages/compras/historico.php',
    
    '/fornecedor' => 'app/views/home/pages/fornecedor/index.php',
    '/fornecedor/listar' => 'app/views/home/pages/fornecedor/listar.php',
    '/fornecedor/editar' => 'app/views/home/pages/fornecedor/editar.php',

    '/produtos/produtos' => 'app/views/home/pages/produtos/index.php',
    '/produtos/produtos/editar' => 'app/views/home/pages/produtos/editar.php',

    '/produtos/entrada' => 'app/views/home/pages/produtos/entrada.php',
    '/produtos/entradas' => 'app/views/home/pages/produtos/entradas.php',
    '/produtos/saida' => 'app/views/home/pages/produtos/saida.php',
    '/produtos/teste' => 'app/views/home/pages/produtos/teste.php',





    '/usuarios' => 'app/views/home/pages/usuarios/index.php',
    '/usuarios/edit' => 'app/views/home/pages/usuarios/edit.php',
    '/usuarios/create' => 'app/views/home/pages/usuarios/create.php',
    '/usuarios/perfil' => 'app/views/home/pages/usuarios/perfil.php',

    '/funcionarios' => 'app/views/home/pages/funcionarios/index.php',
    '/funcionarios/edit' => 'app/views/home/pages/funcionarios/edit.php',
    '/funcionarios/create' => 'app/views/home/pages/funcionarios/create.php',



    '/relatorios' => 'app/views/home/pages/relatorios/index.php',

    '/configuracao' => 'app/views/home/pages/configuracao/index.php',

];




// Verifica rota e inclui página
if (array_key_exists($requestPath, $routes)) {
    include 'app/views/layouts/header.php';

    echo '<div id="content-wrapper">';

    if (in_array($requestPath, $publicRoutes)) {
        include $routes[$requestPath];
    } else {
        echo '<div class="d-flex">';
        include 'app/views/layouts/menu.php';

        echo '<div id="content" class="flex-grow-1 d-flex flex-column">';

        echo '<nav class="navbar navbar-light bg-light px-2">';
        echo ' <div class="d-flex align-items-center">';
        echo '<button class="btn btn-outline-secondary me-2" id="toggleSidebar">☰</button>';
        echo '<span class="navbar-brand mb-0 h1"><h6 class="text-dark">Bem-vindo(a), ' . $_SESSION['user_name'] .'</span>';
        echo '</div>';
        echo '</nav>';

        echo '<div class="container-fluid mt-4">';
        include $routes[$requestPath];
        echo '</div></div>';
    }

    echo '</div>';
    include 'app/views/layouts/footer.php';

    
} else {
    // Página não encontrada
include 'app/views/layouts/header.php';

echo '<div class="d-flex">';
include 'app/views/layouts/menu.php';

echo '<div id="content" class="flex-grow-1 d-flex flex-column">';

echo '<nav class="navbar navbar-light bg-light px-2">';
echo '<div class="d-flex align-items-center">';
echo '<button class="btn btn-outline-secondary me-2" id="toggleSidebar">☰</button>';
echo '<span class="navbar-brand text-danger mb-0 h1">ERRO 404</span>';
echo '</div>';
echo '</nav>';

echo '<div class="container-fluid mt-4 text-center">';
echo '<h1 class="display-4 text-danger"></h1>';
echo '<p class="lead">A página que você está tentando acessar não existe.</p>';
echo '</div>';


echo '</div>';
echo '</div>';

include 'app/views/layouts/footer.php';

}


?>

<script>
    // Toggle Sidebar (continua igual)
    document.getElementById('toggleSidebar').addEventListener('click', () => {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('collapsed');

        // Bloqueia scroll no mobile quando aberto
        if (window.innerWidth <= 767) {
            document.body.classList.toggle('sidebar-open', !sidebar.classList.contains('collapsed'));
        }
    });
</script>