<?php
include_once "conexãoSecrets.php";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

$alertMessage = '';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'];

    // Consulta para buscar o usuário e senha
    $stmt = $conn->prepare("SELECT * FROM administradores WHERE usuario = ? AND senha = ?");
    $stmt->bind_param("ss", $usuario, $senha);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        // Armazenar informações na sessão
        $_SESSION['usuario'] = $row['usuario'];
        $_SESSION['admin'] = true;
        $_SESSION['usuario_nome'] = $row['usuario']; // ou use $row['nome'] se tiver esse campo

        header("Location: /system/Secretsusers/painelSecrets.php"); // redireciona para painel do admin
        exit();
    } else {
        $alertMessage = "Usuário ou senha inválidos!";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ModUp Box</title>
    <link rel="stylesheet" href="/system/styles/inicial.css">
    <link rel="icon" href="/img/logo_3-removebg-preview.png">
    <script>
        <?php if (!empty($alertMessage)) { ?>
        window.onload = function() {
            alert("<?php echo $alertMessage; ?>");
        };
        <?php } ?>
    </script>
</head>
<body>
    <header>
        <h1>Login ModUp Box</h1>
        <p>Digite suas credenciais para acessar o sistema.</p>
    </header>

    <div class="card">
        <h2>Login Administradores</h2>
        <form method="POST">
            <label for="usuario">Usuário:</label>
            <input type="text" id="usuario" name="usuario" required>

            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required>

            <button type="submit">Entrar</button>
        </form>
        
        <p>Deseja voltar? <a href="/index.php">Ir para Página principal</a></p>
    </div>

    <footer class="footer">
        <p>&copy; 2025 Mod-Up Tecnologia. Todos os direitos reservados.</p>
        <p>CNPJ: 59.464.296/0001-44</p>
        <div class="social-links">
            <a href="https://www.linkedin.com/in/mod-up-tecnologia-98905434b/" target="_blank"><i class="fab fa-linkedin"></i> LinkedIn</a> |
            <a href="https://www.facebook.com/profile.php?id=61572602736734" target="_blank"><i class="fab fa-facebook"></i> Facebook</a> |
            <a href="https://www.instagram.com/modup_tech/" target="_blank"><i class="fab fa-instagram"></i> Instagram</a> |
            <a href="https://wa.me/+5551981527122" target="_blank"><i class="fab fa-whatsapp"></i> WhatsApp</a> |
            <a href="mailto:moduptecnologia@gmail.com" target="_blank"><i class="fas fa-envelope"></i> Email</a> |
            <a href="https://www.youtube.com/@arthurreis8923" target="_blank"><i class="fab fa-youtube"></i> YouTube</a> |
            <a href="https://www.tiktok.com/@moduptech" target="_blank"><i class="fab fa-tiktok"></i> TikTok</a>
        </div>
    </footer>
</body>
</html>
