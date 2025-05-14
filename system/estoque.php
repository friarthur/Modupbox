<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include_once "conexão.php";

// Verifica se está logado
if (!isset($_SESSION['usuario_id'])) {
    echo "Você precisa estar logado.";
    exit;
}

$id_empresa = $_SESSION['usuario_id'];

// Cadastro de produto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cadastrar_produto'])) {
    $nome = $_POST['nome_produto'];
    $tipo = $_POST['tipo_produto'];
    $validade = $_POST['data_validade'];
    $preco = $_POST['preco'];

    $stmt = $pdo->prepare("INSERT INTO produtos (id_empresa, nome_produto, tipo_produto, data_validade, preco)
                           VALUES (:id_empresa, :nome, :tipo, :validade, :preco)");
    $stmt->execute([
        ':id_empresa' => $id_empresa,
        ':nome' => $nome,
        ':tipo' => $tipo,
        ':validade' => $validade,
        ':preco' => $preco
    ]);
}

// Pesquisa e paginação
$busca = $_GET['busca'] ?? '';
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$limite = 10;
$offset = ($pagina - 1) * $limite;

// Consulta total para paginação
$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM produtos WHERE id_empresa = :id AND nome_produto LIKE :busca");
$totalStmt->execute([
    ':id' => $id_empresa,
    ':busca' => "%$busca%"
]);
$totalProdutos = $totalStmt->fetchColumn();
$totalPaginas = ceil($totalProdutos / $limite);

// Consulta paginada
$stmt = $pdo->prepare("SELECT * FROM produtos WHERE id_empresa = :id AND nome_produto LIKE :busca ORDER BY id ASC LIMIT :limite OFFSET :offset");
$stmt->bindValue(':id', $id_empresa, PDO::PARAM_INT);
$stmt->bindValue(':busca', "%$busca%", PDO::PARAM_STR);
$stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Estoque</title>
    <link rel="stylesheet" href="/system/styles/estoque.css">
    <link rel="icon" href="/img/logo_3-removebg-preview.png">
    <style>
        .paginacao {
            margin-top: 20px;
            text-align: center;
        }
        .paginacao a {
            padding: 8px 12px;
            margin: 0 5px;
            background: #eee;
            color: #333;
            text-decoration: none;
            border-radius: 5px;
        }
        .paginacao a.ativa {
            background-color: #007bff;
            color: #fff;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <a href="/system/painel.php"><button>Voltar</button></a>
    <h2>Cadastro de Produto</h2>
    <form method="POST">
        <input type="text" name="nome_produto" placeholder="Nome do Produto" required>
        <input type="text" name="tipo_produto" placeholder="Tipo do Produto">
        <input type="date" name="data_validade" placeholder="Data de Validade">
        <input type="number" step="0.01" name="preco" placeholder="Preço" required>
        <button type="submit" name="cadastrar_produto">Cadastrar</button>
    </form>

    <hr>

    <h2>Pesquisar Produto</h2>
    <form method="GET">
        <input type="text" name="busca" placeholder="Buscar produto..." value="<?= htmlspecialchars($busca) ?>">
        <button type="submit">Buscar</button>
    </form>

    <hr>

    <h2>Lista de Produtos</h2>
    <table border="1" cellpadding="10">
        <thead>
            <tr>
                <th>ID</th>
                <th>Produto</th>
                <th>Tipo</th>
                <th>Validade</th>
                <th>Preço</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($produtos as $produto): ?>
                <tr>
                    <td><?= $produto['id'] ?></td>
                    <td><?= htmlspecialchars($produto['nome_produto']) ?></td>
                    <td><?= htmlspecialchars($produto['tipo_produto']) ?></td>
                    <td><?= $produto['data_validade'] ?></td>
                    <td>R$ <?= number_format($produto['preco'], 2, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="paginacao">
        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
            <a href="?pagina=<?= $i ?>&busca=<?= urlencode($busca) ?>" class="<?= ($pagina == $i) ? 'ativa' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
</body>
</html>
