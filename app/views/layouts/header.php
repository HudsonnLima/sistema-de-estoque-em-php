<!-- views/layouts/header.php -->
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php
        // Verifica a rota atual
        if (isset($request) && $request === '/login') {
            echo SITE_LOGIN;
        } else {
            echo SITE_NAME;
        }
        ?>
    </title>


    <meta name="description" content="<?php echo htmlspecialchars(SITE_DESCRIPTION) ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars(SITE_KEYWORDS) ?>">
    <meta name="author" content="<?php echo htmlspecialchars(SITE_AUTHOR) ?>">

    <meta property="og:title" content="<?php echo htmlspecialchars(SITE_NAME) ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars(SITE_DESCRIPTION) ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo BASE_URL ?>">
    <meta property="og:image" content="<?php echo BASE_URL; ?>/public/images/icon.png">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars(SITE_NAME) ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars(SITE_DESCRIPTION) ?>">
    <meta name="twitter:image" content="<?php echo BASE_URL; ?>/public/images/icon.png">


    <link rel="icon" type="image/x-icon" href="<?php echo BASE_URL; ?>/public/images/icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

    <!--CHAMADA JS FIREBASE-->
    <script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-messaging-compat.js"></script>
    <!--//CHAMADA JS FIREBASE-->

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/menu.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/login.css">

</head>

<body>