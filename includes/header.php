<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Secretaria da Igreja'; ?></title>

    <link rel="stylesheet" href="style.css">
    <?php if(isset($additional_css) && is_array($additional_css)): ?>
        <?php foreach($additional_css as $css): ?>
            <link rel="stylesheet" href="<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    
    <link rel="icon" type="image/png" href="img/3.png">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
   

    <header class="navbar">
        <img class="logo2" src="img/3.png" alt="">
        <div class="navbar-title">Sistema de Gerenciamento </div>

        <!-- Botão do menu hambúrguer -->
        <div class="hamburger" id="hamburger">
            <i class="ri-menu-line"></i>
        </div>

        <ul class="nav-links" id="nav-links">
            <li><a href="index.php" <?php echo (!isset($_GET['page']) || $_GET['page'] == 'home') ? 'class="active"' : ''; ?>>Home</a></li>
            <li><a href="computadores.php" <?php echo (isset($_GET['page']) && $_GET['page'] == 'computaores') ? 'class="active"' : ''; ?>>Computadores</a></li>
            <li><a href="setores.php" <?php echo (isset($_GET['page']) && $_GET['page'] == 'setores') ? 'class="active"' : ''; ?>>Setores</a></li>
            

           
        </ul>
    </header>

    <hr class="tt">

    <script>
        document.getElementById('hamburger').addEventListener('click', function () {
            document.getElementById('nav-links').classList.toggle('active');
        });
    </script>