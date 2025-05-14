<?php
session_start();
include_once "conexão.php";

if (!isset($_SESSION['usuario_id'])) {
    echo "Você precisa estar logado.";
    exit;
}

$id_empresa = $_SESSION['usuario_id'];

// Verifica se os dados foram enviados corretamente
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produtos_json = $_POST['produtos_json'] ?? '';
    $total = floatval($_POST['valor_total'] ?? 0);
    $cpf = isset($_POST['usar_cpf']) && !empty($_POST['cpf']) ? preg_replace('/\D/', '', $_POST['cpf']) : null;
    $forma_pagamento = $_POST['forma_pagamento'] ?? '';

    if (empty($produtos_json)) {
        echo "Nenhum produto na venda.";
        exit;
    }

    if (empty($forma_pagamento)) {
        echo "Forma de pagamento obrigatória.";
        exit;
    }

    $produtos = json_decode($produtos_json, true);

    if (!is_array($produtos) || empty($produtos)) {
        echo "Erro ao processar os produtos.";
        exit;
    }

    try {
        $pdo->beginTransaction();

        // Inserir venda
        $stmtVenda = $pdo->prepare("
            INSERT INTO vendas (id_empresa, total, data_venda, cpf, forma_pagamento)
            VALUES (:empresa, :total, NOW(), :cpf, :forma)
        ");
        $stmtVenda->execute([
            ':empresa' => $id_empresa,
            ':total' => $total,
            ':cpf' => $cpf,
            ':forma' => $forma_pagamento
        ]);

        $id_venda = $pdo->lastInsertId();

        // Inserir itens da venda
        $stmtItem = $pdo->prepare("
            INSERT INTO itens_venda (id_venda, id_produto, nome_produto, preco_unitario, quantidade, subtotal)
            VALUES (:id_venda, :id_produto, :nome, :preco, :quantidade, :subtotal)
        ");

        foreach ($produtos as $item) {
            $stmtItem->execute([
                ':id_venda' => $id_venda,
                ':id_produto' => $item['id'],
                ':nome' => $item['nome_produto'],
                ':preco' => $item['preco'],
                ':quantidade' => $item['quantidade'],
                ':subtotal' => $item['subtotal']
            ]);
        }

        $pdo->commit();

        $_SESSION['carrinho'] = [];
        $_SESSION['recibo'] = [
            'id_venda' => $id_venda,
            'total' => $total,
            'cpf' => $cpf,
            'forma_pagamento' => $forma_pagamento
        ];

        header("Location: recibo.php");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Erro ao finalizar a compra: " . $e->getMessage();
    }
} else {
    echo "Requisição inválida.";
}
