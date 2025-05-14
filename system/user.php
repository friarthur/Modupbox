<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

include_once "conexão.php";

if (!isset($_SESSION['usuario_id'])) {
    echo "Você precisa estar logado.";
    exit;
}

$id = $_SESSION['usuario_id'];

// Atualiza os dados
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $sql = "UPDATE empresas SET 
                    nome = :nome,
                    cnpj = :cnpj,
                    senha = :senha,
                    nome_loja = :nome_loja,
                    qtd_usuarios = :qtd_usuarios,
                    endereco = :endereco,
                    status_pagamento = :status_pagamento,
                    telefone = :telefone
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nome' => $_POST['nome'],
            ':cnpj' => $_POST['cnpj'],
            ':senha' => $_POST['senha'],
            ':nome_loja' => $_POST['nome_loja'],
            ':qtd_usuarios' => $_POST['qtd_usuarios'],
            ':endereco' => $_POST['endereco'],
            ':status_pagamento' => $_POST['status_pagamento'],
            ':telefone' => $_POST['telefone'],
            ':id' => $id
        ]);

       

    } catch (PDOException $e) {
        echo "<p style='color: red;'>Erro ao atualizar dados: " . $e->getMessage() . "</p>";
    }
}

// Puxa os dados atualizados
$stmt = $pdo->prepare("SELECT nome, cnpj, senha, nome_loja, qtd_usuarios, endereco, status_pagamento, telefone FROM empresas WHERE id = :id");
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    echo "Usuário não encontrado.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Mod-up Box-Página do Usuário</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/system/styles/user.css">
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
            <li><a href="/system/painel.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="/system/user.php"><i class="fas fa-user"></i> Usuário</a></li>
            
            <li><a href="/index.php"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
        </ul>
    </nav>

<div class="container">
    <h2>Informações do Usuário</h2>

    <table>
        <tr>
            <th>Campo</th>
            <th>Valor</th>
        </tr>
        <?php foreach ($usuario as $campo => $valor): ?>
            <tr>
                <td><?= ucfirst(str_replace('_', ' ', $campo)) ?></td>
                <td><?= htmlspecialchars($valor) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <h3>Editar Dados</h3>
    <form method="POST">
        <?php foreach ($usuario as $campo => $valor): ?>
            <label for="<?= $campo ?>"><?= ucfirst(str_replace('_', ' ', $campo)) ?>:</label><br>
            <input type="text" name="<?= $campo ?>" id="<?= $campo ?>" value="<?= htmlspecialchars($valor) ?>" required><br><br>
        <?php endforeach; ?>
        <button type="submit" class="btn"><i class="fas fa-save"></i> Salvar Alterações</button>
    </form>

    <div class="buttons">
        <a href="https://wa.me/5551981527122<?= preg_replace('/\D/', '', $usuario['telefone']) ?>" class="btn" target="_blank">
            <i class="fab fa-whatsapp"></i> Entrar em Contato
        </a>
        <a href="/system/painel.php" class="btn"><i class="fas fa-arrow-left"></i> Voltar</a>
    </div>
</div>

</body>
</html>
