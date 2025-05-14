
<?php
include_once "conexãoSecrets.php";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == "POST") {
    $usuario = $_POST['usuario'];
    $quantidade_usuarios = $_POST['quantidade_usuarios'];
    $senha = $_POST['senha'];

    $stmt = $conn->prepare("INSERT INTO administradores (usuario, quantidade_usuarios, senha) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $usuario, $quantidade_usuarios, $senha);

    if ($stmt->execute()) {
        header("Location: /system/Secretsusers/LoginSecrets.php");
        exit();
    } else {
        echo "Erro ao inserir: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>secrets - ModUp Box</title>
    <link rel="stylesheet" href="/system/styles/inicial.css">
    <link rel="icon" href="/img/logo_3-removebg-preview.png">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
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
        <h1>Cadastro ModUp Box</h1>
        <p>Preencha o formulário abaixo para cadastrar sua empresa.</p>
    </header>
    
    <div class="card">
        <h2>Cadastro</h2>
        <form method="POST">
            <label for="usuario">Nome:</label>
            <input type="text" id="usuario" name="usuario" required>  
            <label for="nome">Quantos usuarios tem na modup?:</label>
            <input type="text" id="quantidade_usuarios" name="quantidade_usuarios" required>  
          
           
            <label for="senha">Criar Senha:</label>
            <input type="password" id="senha" name="senha" required>
            
            <a href="/system/Secretsusers/LoginSecrets.php"><button type="submit">Finalizar o cadastro!</button></a>
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