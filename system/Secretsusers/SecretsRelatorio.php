<?php
include_once __DIR__ . "/conexãoSecrets.php";

session_start();

// Debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verifica se o usuário está logado e é admin
if (!isset($_SESSION['usuario']) || !isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: /system/Secretsusers/loginSecrets.php');
    exit;
}

// Filtros do formulário
$inicio = $_GET['inicio'] ?? null;
$fim = $_GET['fim'] ?? null;
$status = $_GET['status_pagamento'] ?? null;

// Construção da query com filtros
$sql = "SELECT * FROM empresas WHERE 1=1";
$params = [];

if ($inicio) {
    $sql .= " AND data_cadastro >= '$inicio'";
}
if ($fim) {
    $sql .= " AND data_cadastro <= '$fim'";
}
if ($status) {
    $sql .= " AND status_pagamento = '$status_pagamento'";
}

// Executa a consulta com os filtros aplicados
$result = $conn->query($sql);

// Verifica se a consulta foi bem-sucedida
if (!$result) {
    die("Erro na consulta: " . $conn->error);
}

// Busca os dados dos relatórios
$relatorios = $result->fetch_all(MYSQLI_ASSOC);

// Resumo
// Empresas Ativas
$sqlAtivas = "SELECT COUNT(*) as total FROM empresas WHERE status_pagamento = 'pago'";
$resultAtivas = $conn->query($sqlAtivas);
if ($resultAtivas) {
    $rowAtivas = $resultAtivas->fetch_assoc();
    $empresasAtivas = $rowAtivas['total'] ?? 0;
} else {
    $empresasAtivas = 0;
}

// Pagamentos Pendentes
$sqlPendentes = "SELECT COUNT(*) as total FROM empresas WHERE status_pagamento = 'pendente'";
$resultPendentes = $conn->query($sqlPendentes);
if ($resultPendentes) {
    $rowPendentes = $resultPendentes->fetch_assoc();
    $pagamentosPendentes = $rowPendentes['total'] ?? 0;
} else {
    $pagamentosPendentes = 0;
}

// Total Arrecadado
$sqlArrecadado = "SELECT SUM(valor_pix) as total FROM empresas WHERE status_pagamento = 'pago'";
$resultArrecadado = $conn->query($sqlArrecadado);
if ($resultArrecadado) {
    $rowArrecadado = $resultArrecadado->fetch_assoc();
    $totalArrecadado = $rowArrecadado['total'] ?? 0;
} else {
    $totalArrecadado = 0;
}

// Cadastros no mês atual
$sqlCadastrosMes = "SELECT COUNT(*) as total FROM empresas WHERE MONTH(data_cadastro) = MONTH(CURRENT_DATE()) AND YEAR(data_cadastro) = YEAR(CURRENT_DATE())";
$resultCadastrosMes = $conn->query($sqlCadastrosMes);
if ($resultCadastrosMes) {
    $rowCadastrosMes = $resultCadastrosMes->fetch_assoc();
    $cadastrosMes = $rowCadastrosMes['total'] ?? 0;
} else {
    $cadastrosMes = 0;
}


?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório - admin</title>
    <link rel="stylesheet" href="/system/Secretsusers/css/ralatorio.css">
    <link rel="icon" href="/img/logo_3-removebg-preview.png">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <a href="/system/Secretsusers/painelSecrets.php" class="voltar-btn">⬅ Voltar para o Painel Principal</a>

    <h2>📊 Relatórios do Sistema</h2>

    <!-- Resumo geral -->
    <div class="grid-cards">
        <div class="card-relatorio">
            <div class="resumo">Empresas Ativas</div>
            <div class="resumo-numero">
                <?php echo $empresasAtivas ?? '0'; ?>
            </div>
        </div>
        <div class="card-relatorio">
            <div class="resumo">Pagamentos Pendentes</div>
            <div class="resumo-numero" style="color: #dc2626;">
                <?php echo $pagamentosPendentes ?? '0'; ?>
            </div>
        </div>
        <div class="card-relatorio">
            <div class="resumo">Total Arrecadado</div>
            <div class="resumo-numero" style="color: #2563eb;">
                R$ <?php echo number_format($totalArrecadado ?? 0, 2, ',', '.'); ?>
            </div>
        </div>
        <div class="card-relatorio">
            <div class="resumo">Cadastros no mês</div>
            <div class="resumo-numero" style="color: #0ea5e9;">
                <?php echo $cadastrosMes ?? '0'; ?>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <form method="GET" class="filtros">
        <div>
            <label>Data Inicial</label>
            <input type="date" name="inicio">
        </div>
        <div>
            <label>Data Final</label>
            <input type="date" name="fim">
        </div>
        <div>
            <label>Status</label>
            <select name="status">
                <option value="">Todos</option>
                <option value="ativo">Ativo</option>
                <option value="inativo">Inativo</option>
            </select>
        </div>
        <button type="submit">Filtrar</button>
    </form>

    <!-- Tabela de relatórios -->
    <div class="tabela-container">
        <h3 style="margin-bottom: 16px;">📁 Lista de Registros</h3>
        <table>
            <thead>
                <tr>
                    <th>#ID</th>
                    <th>Empresa</th>
                    <th>Data Cadastro</th>
                    <th>Status</th>
                    <th>Valor</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($relatorios as $registro): ?>
                    <tr>
                        <td><?= $registro['id'] ?></td>
                        <td><?= $registro['nome_loja'] ?></td>
                        <td><?= date('d/m/Y', strtotime($registro['data_cadastro'])) ?></td>
                        <td>
                            <span style="color: white; padding: 4px 10px; border-radius: 6px; background-color: <?= $registro['status_pagamento'] === 'ativo' ? '#16a34a' : '#6b7280' ?>;">
                                <?= ucfirst($registro['status_pagamento']) ?>
                            </span>
                        </td>
                        <td>R$ <?= number_format($registro['valor_pix']?? 0, 2, ',', '.') ?></td>
                        <td>
                            <a href="detalhes.php?id=<?= $registro['id'] ?>" style="color: #2563eb; text-decoration: underline;">Ver</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
