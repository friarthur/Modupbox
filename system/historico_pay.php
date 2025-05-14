<?php
session_start();
include_once "conexão.php";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Certifique-se de que você tem o ID da empresa disponível via sessão
$empresa_id = $_SESSION['usuario_id'] ?? null;

if (!$empresa_id) {
    echo "ID da empresa não encontrado!";
    exit;
}

// Se o botão de cancelar serviço foi clicado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancelar'])) {
    try {
        $pdo->beginTransaction();

        // Deletar os itens de vendas ligados às vendas da empresa
        $stmt = $pdo->prepare("DELETE FROM itens_venda WHERE id_venda IN (SELECT id FROM vendas WHERE id_empresa = :empresa_id)");
        $stmt->execute(['empresa_id' => $empresa_id]);

        // Deletar as vendas da empresa
        $stmt = $pdo->prepare("DELETE FROM vendas WHERE id_empresa = :empresa_id");
        $stmt->execute(['empresa_id' => $empresa_id]);

        // Deletar os produtos da empresa
        $stmt = $pdo->prepare("DELETE FROM produtos WHERE id_empresa = :empresa_id");
        $stmt->execute(['empresa_id' => $empresa_id]);

        // Deletar o histórico de pagamentos
        $stmt = $pdo->prepare("DELETE FROM historico_pagamentos WHERE empresas_id = :empresa_id");
        $stmt->execute(['empresa_id' => $empresa_id]);

        // Deletar o cadastro da empresa
        $stmt = $pdo->prepare("DELETE FROM empresas WHERE id = :empresa_id");
        $stmt->execute(['empresa_id' => $empresa_id]);

        $pdo->commit();

        // Redirecionar ou exibir mensagem
        echo "<script>alert('Serviço cancelado com sucesso!'); window.location.href = '/logout.php';</script>";
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Erro ao cancelar o serviço: " . $e->getMessage();
        exit;
    }
}

// Consulta informações da empresa
$stmt_empresa = $pdo->prepare("SELECT * FROM empresas WHERE id = :empresa_id");
$stmt_empresa->execute(['empresa_id' => $empresa_id]);
$empresa = $stmt_empresa->fetch(PDO::FETCH_ASSOC);

if ($empresa) {
    $status_pagamento = $empresa['status_pagamento'];
    $data_pagamento = $empresa['data_pagamento'];
} else {
    echo "Empresa não encontrada!";
    exit;
}

$cor_status = strtolower(trim($status_pagamento)) === 'pago' ? '#2ecc71' : '#e74c3c';
$forma_pagamento_texto = $status_pagamento ?? "Pendente";

$data_limite = null;
if ($data_pagamento) {
    $data_pagamento_obj = new DateTime($data_pagamento);
    $data_limite = (clone $data_pagamento_obj)->modify('+30 days');
}

// Obtem histórico completo de pagamentos
$stmt_historico = $pdo->prepare("SELECT * FROM historico_pagamentos WHERE empresas_id = :empresa_id ORDER BY data_pagamento DESC");
$stmt_historico->execute(['empresa_id' => $empresa_id]);
$historico = $stmt_historico->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Histórico de Pagamentos</title>
  <link rel="icon" href="/img/logo_3-removebg-preview.png">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f5f7fa;
      padding: 30px;
    }

    .container {
      max-width: 800px;
      margin: auto;
    }

    .titulo {
      text-align: center;
      font-size: 28px;
      margin-bottom: 20px;
      color: #2c3e50;
    }

    .status-box {
      background-color: #ffffff;
      border-left: 8px solid <?= $cor_status ?>;
      padding: 15px;
      border-radius: 10px;
      margin-bottom: 30px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .status-box strong {
      font-size: 18px;
      color: #444;
    }

    .status-box .info {
      margin-top: 5px;
      color: #666;
    }

    .pagamento {
      background-color: white;
      border-radius: 15px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      padding: 20px;
      margin-bottom: 20px;
      transition: transform 0.2s ease;
    }

    .pagamento:hover {
      transform: scale(1.01);
    }

    .pagamento strong {
      color: #555;
    }

    .valor {
      color: #27ae60;
      font-weight: bold;
    }

    .forma {
      background: #ecf0f1;
      display: inline-block;
      padding: 4px 10px;
      border-radius: 6px;
      font-size: 14px;
      color: #333;
    }

    .sem-registro {
      text-align: center;
      color: #888;
      margin-top: 40px;
    }

    .meu-botao {
        background-color: #4CAF50;
        border: none;
        color: white;
        padding: 15px 32px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        margin: 4px 2px;
        cursor: pointer;
        border-radius: 12px;
        transition: background-color 0.3s, transform 0.2s;
    }

    .meu-botao:hover {
        background-color: #45a049;
        transform: scale(1.05);
    }

    .meu-botao:active {
        background-color: #3e8e41;
        transform: scale(0.98);
    }
  </style>
</head>
<body>

<div class="container">
  <div class="titulo">Histórico de Pagamentos</div>

  <!-- Status de pagamento -->
  <div class="status-box">
    <strong>Status atual: <?= htmlspecialchars($forma_pagamento_texto) ?></strong>
    <div class="info">
      <?php if ($data_limite): ?>
        Acesso válido até: <strong><?= $data_limite->format('d/m/Y') ?></strong>
      <?php else: ?>
        Nenhum pagamento encontrado.
      <?php endif; ?>
    </div>
    <div class="info">
      <small>O processo de validação do pagamento pode levar de 1 a 3 horas. Caso tenha dúvidas, entre em contato pelo WhatsApp: 
        <a href="https://wa.me/5551981527122">+55 51 98152-7122</a>.
      </small>
    </div>
  </div>

  <!-- Histórico de pagamentos -->
  <?php if ($historico): ?>
    <?php foreach ($historico as $row): ?>
      <div class="pagamento">
        <div><strong>Data:</strong> <?= date("d/m/Y H:i", strtotime($row['data_pagamento'])) ?></div>
        <div><strong>Valor:</strong> <span class="valor">R$<?= number_format($row['valor_total'], 2, ',', '.') ?></span></div>
        <div><strong>Forma:</strong> <span class="forma"><?= htmlspecialchars($row['forma_pagamento']) ?></span></div>
        <div><strong>Detalhes:</strong> <?= htmlspecialchars($row['detalhes']) ?></div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <div class="sem-registro">Nenhum pagamento registrado ainda.</div>
  <?php endif; ?>

  <a href="/system/painel.php"><button class="meu-botao">Clique aqui para voltar</button></a>

  <!-- Formulário de cancelamento do serviço -->
  <form method="post" onsubmit="return confirm('Tem certeza que deseja cancelar o serviço? Esta ação é irreversível!');">
    <button type="submit" name="cancelar" class="meu-botao" style="background-color: #e74c3c;">Cancelar Serviço</button>
  </form>
</div>

</body>
</html>
