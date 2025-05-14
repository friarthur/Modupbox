<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once "conexão.php";
session_start(); // Iniciar a sessão para gerenciar a autenticação

// Se o usuário já estiver logado, redirecionar para o painel
if (isset($_SESSION['usuario_id'])) {
    header('Location: painel.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = trim($_POST['login']); // Remover espaços extras
    $senha = $_POST['senha'];

    // Verificar se o login é um CNPJ (14 números) ou nome de usuário
    if (strlen($login) == 14 && preg_match('/^\d+$/', $login)) {
        $sql = "SELECT * FROM empresas WHERE cnpj = :login";
    } else {
        $sql = "SELECT * FROM empresas WHERE nome = :login";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':login', $login);
    $stmt->execute();
    $empresa = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar senha
    if ($empresa) {
        if (password_verify($senha, $empresa['senha'])) {
            $_SESSION['usuario_id'] = $empresa['id'];
            $_SESSION['usuario_nome'] = $empresa['nome'];
            header('Location: painel.php');
            exit;
        } else {
            $erro = "Nome de usuário ou senha inválidos!";
        }
    } else {
        $erro = "Nome de usuário ou senha inválidos!";
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ModUp Box</title>
    <link rel="stylesheet" href="/system/styles/inicial.css">
</head>
<body>
    <header>
        <h1>Login ModUp Box</h1>
        <p>Faça login para acessar o painel.</p>
    </header>

    <div class="card">
        <h2>Login</h2>
        <?php if (isset($erro)) echo "<p class='erro'>$erro</p>"; ?>
        <form method="POST">
            <label for="login">Nome de Usuário:</label>
            <input type="text" id="login" name="login" required>
            
            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required>
            
            <button type="submit">Entrar</button>
        </form>
        <p>Não tem uma conta? <a href="inicial.php">Cadastre-se</a></p>
        <p>Deseja voltar? <a href="/index.php">Ir para Página principal</a></p>

    </div>

    <footer>
        <p>&copy; 2025 ModUp Box. Todos os direitos reservados.</p>
    </footer>
</body>
</html>
