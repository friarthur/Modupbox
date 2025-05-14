<?php
session_start();
include_once "conexão.php";

if (!isset($_SESSION['usuario_id'])) {
    echo "Você precisa estar logado.";
    exit;
}

$id_empresa = $_SESSION['usuario_id'];

// Filtros
$filtroData = $_GET['filtro'] ?? 'todos';

switch ($filtroData) {
    case 'diario':
        $condicaoData = "AND DATE(data_venda) = CURDATE()";
        break;
    case 'semanal':
        $condicaoData = "AND YEARWEEK(data_venda, 1) = YEARWEEK(CURDATE(), 1)";
        break;
    case 'mensal':
        $condicaoData = "AND MONTH(data_venda) = MONTH(CURDATE()) AND YEAR(data_venda) = YEAR(CURDATE())";
        break;
    default:
        $condicaoData = "";
        break;
}

// Consulta as vendas
$stmt = $pdo->prepare("
    SELECT * FROM vendas 
    WHERE id_empresa = :empresa $condicaoData
    ORDER BY id ASC
");
$stmt->execute([':empresa' => $id_empresa]);
$vendas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Soma total das vendas filtradas
$totalVendas = array_sum(array_column($vendas, 'total'));
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Vendas</title>
    <link rel="stylesheet" href="/system/styles/historico.css"> <!-- Link do CSS -->
</head>
<body>
    <h2>📊 Histórico de Vendas</h2>

    <div class="filtro-links">
        <a href="?filtro=diario">🗓️ Diário</a> | 
        <a href="?filtro=semanal">📆 Semanal</a> | 
        <a href="?filtro=mensal">🗂️ Mensal</a> | 
        <a href="?filtro=todos">📃 Todos</a>
    </div>

    <table class="vendas-table">
        <thead>
            <tr>
                <th>ID Venda</th>
                <th>Total (R$)</th>
                <th>Data</th>
                <th>CPF</th>
                <th>Forma de Pagamento</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($vendas) === 0): ?>
                <tr><td colspan="5">Nenhuma venda encontrada.</td></tr>
            <?php else: ?>
                <?php foreach ($vendas as $venda): ?>
                    <tr>
                        <td><?= $venda['id'] ?></td>
                        <td><?= number_format($venda['total'], 2, ',', '.') ?></td>
                        <td><?= date("d/m/Y H:i", strtotime($venda['data_venda'])) ?></td>
                        <td><?= $venda['cpf'] ?: '—' ?></td>
                        <td><?= ucfirst($venda['forma_pagamento']) ?></td>
                    </tr>
                <?php endforeach ?>
            <?php endif ?>
        </tbody>
    </table>

    <?php if (count($vendas) > 0): ?>
        <p style="text-align: center; font-weight: bold; margin-top: 20px;">
            💰 Total de Vendas: R$ <?= number_format($totalVendas, 2, ',', '.') ?>
        </p>
    <?php endif; ?>

    <a href="/system/painel.php">
        <button class="vote-button">Votar para o Painel</button>
    </a>
</body>
</html>
