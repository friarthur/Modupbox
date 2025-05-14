<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once "conexão.php";

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: /system/login.php');
    exit;
}

// Expiração da sessão por tempo
$tempo_maximo = 4 * 60 * 60; // 4 horas em segundos

if (isset($_SESSION['ultimo_acesso'])) {
    $tempo_passado = time() - $_SESSION['ultimo_acesso'];
    if ($tempo_passado > $tempo_maximo) {
        // Se passou de 4 horas sem atividade, destrói a sessão
        session_unset();
        session_destroy();
        header('Location: /system/login.php');
        exit;
    }
}

// Atualiza o tempo de último acesso
$_SESSION['ultimo_acesso'] = time();

// Pegando o nome do usuário
$nome_usuario = $_SESSION['usuario_nome'];
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel - ModUp Box</title>
    <link rel="stylesheet" href="/system/styles/painel.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link rel="icon" href="/img/logo_3-removebg-preview.png">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <nav class="navbar">
    <div class="logo"><img src="/img/logo_3-removebg-preview.png" alt="SoftTec Logo" height="50"></div>
        <ul>
            <li><a href="painel.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="/system/user.php"><i class="fas fa-user"></i> Usuário</a></li>
           <li><a href="/system/historico_pay.php"><i class="fa-solid fa-dollar-sign"></i> Financeiro</a></li>
            <li><a href="/system/logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
        </ul>
    </nav>

    
    <main>
        <div class="container">
            <h1>Bem-vindo, <?php echo $nome_usuario; ?>!</h1>
            <p>Acesse suas configurações e gerencie sua conta.</p>
            <div class="cards">
                <div class="card">
                    <h2>Caixa</h2>
                    <p>Gerencie as transações do seu negócio.</p>
                    <a href="/system/caixa.php"><button>Acessar</button></a>
                </div>
                <div class="card">
                    <h2>Estoque</h2>
                    <p>Controle seus produtos e materiais.</p>
                    <a href="/system/estoque.php"><button>Acessar</button></a>
                </div>
                <div class="card">
                    <h2>Histórico de Vendas</h2>
                    <p>Consulte as vendas realizadas.</p>
                    <a href="/system/historico.php"><button>Acessar</button></a>
                </div>
                
            </div>
        </div>
    </main>

  

<footer class="footer">
        <p>&copy; 2025 Mod-Up Tecnologia. Todos os direitos reservados.</p>
        <p>CNPJ: 59.464.296/0001-44</p>
        <div class="social-links">
            <a href="https://www.linkedin.com/in/mod-up-tecnologia-98905434b/" target="_blank">
                <i class="fab fa-linkedin"></i> LinkedIn
            </a> |
            <a href="https://www.facebook.com/profile.php?id=61572602736734" target="_blank">
                <i class="fab fa-facebook"></i> Facebook
            </a> |
            <a href="https://www.instagram.com/modup_tech/" target="_blank">
                <i class="fab fa-instagram"></i> Instagram
            </a> |
            <a href="https://wa.me/+5551981527122" target="_blank">
                <i class="fab fa-whatsapp"></i> WhatsApp
            </a> |
            <a href="mailto:moduptecnologia@gmail.com?subject=Interesse%20nos%20serviços%20da%20Mod-Up&body=Olá%20equipe%20Mod-Up,%0D%0A%0D%0AEstou%20interessado%20em%20saber%20mais%20sobre%20seus%20serviços." target="_blank">
                <i class="fas fa-envelope"></i> Email
            </a> |
            
            <a href="https://www.tiktok.com/@moduptech" target="_blank">
                <i class="fab fa-tiktok"></i> TikTok
            </a>
        </div>
    </footer>

   
</body>
</html>

