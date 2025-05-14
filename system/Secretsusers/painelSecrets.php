<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once "conexãoSecrets.php";

// Verifica se o administrador está logado
if (!isset($_SESSION['usuario']) || !isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: /system/Secretsusers/loginSecrets.php');
    exit;
}

// Expiração da sessão
$tempo_maximo = 4 * 60 * 60;
if (isset($_SESSION['ultimo_acesso'])) {
    $tempo_passado = time() - $_SESSION['ultimo_acesso'];
    if ($tempo_passado > $tempo_maximo) {
        session_unset();
        session_destroy();
        header('Location: /system/login.php');
        exit;
    }
}
$_SESSION['ultimo_acesso'] = time();
$nome_usuario = $_SESSION['usuario_nome'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Admin - ModUp Box</title>
    <link rel="stylesheet" href="/system/styles/painel.css">
    <link rel="icon" href="/img/logo_3-removebg-preview.png">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

<nav class="navbar">
    <div class="logo"><img src="/img/logo_3-removebg-preview.png" alt="ModUp Logo" height="50"></div>
    <ul>
        <li><a href="painel_admin.php" class="active"><i class="fas fa-cogs"></i> Admin Dashboard</a></li>
        <li><a href="/system/Secretsusers/gerencuser.php"><i class="fas fa-users-cog"></i> Gerenciar Usuários</a></li>
        <li><a href="/system/Secretsusers/SecretsRelatorio.php"><i class="fas fa-chart-line"></i> Relatórios</a></li>
        <li><a href="/system/config.php"><i class="fas fa-wrench"></i> Configurações</a></li>
        <li><a href="/system/Secretsusers/logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
    </ul>
</nav>

<main>
    <div class="container">
        <h1>Bem-vindo,  <?php echo $nome_usuario; ?>!</h1>
        <p>Gerencie o sistema e os usuários com total controle.</p>
        <div class="cards">
            <div class="card">
                <h2>Usuários</h2>
                <p>Visualize e edite todos os usuários cadastrados.</p>
                <a href="/system/Secretsusers/gerencuser.php"><button>Gerenciar</button></a>
            </div>
            <div class="card">
                <h2>Relatórios</h2>
                <p>Veja os dados financeiros e de uso do sistema.</p>
                <a href="/system/Secretsusers/SecretsRelatorio.php"><button>Visualizar</button></a>
            </div>
            <div class="card">
                <h2>Configurações</h2>
                <p>Altere preferências do sistema, pagamento e mais.</p>
                <a href="/system/config.php"><button>Ajustar</button></a>
            </div>
        </div>
    </div>
</main>

<footer class="footer">
    <p>&copy; 2025 Mod-Up Tecnologia. Todos os direitos reservados.</p>
    <p>CNPJ: 59.464.296/0001-44</p>
    <div class="social-links">
        <!-- Mesmo bloco de redes sociais -->
    </div>
</footer>

</body>
</html>
