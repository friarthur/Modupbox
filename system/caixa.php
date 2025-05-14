<?php
session_start();
include_once "conexão.php";

if (!isset($_SESSION['usuario_id'])) {
    echo "Você precisa estar logado.";
    exit;
}

$id_empresa = $_SESSION['usuario_id'];

// Inicializa o carrinho
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

// Adicionar produto ao carrinho
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['adicionar'])) {
        $id_produto = $_POST['id_produto'];
        $quantidade = max(1, intval($_POST['quantidade']));

        $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = :id AND id_empresa = :empresa");
        $stmt->execute([
            ':id' => $id_produto,
            ':empresa' => $id_empresa
        ]);
        $produto = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($produto) {
            $produto['quantidade'] = $quantidade;
            $produto['subtotal'] = $produto['preco'] * $quantidade;
            $_SESSION['carrinho'][] = $produto;
        } else {
            $erro = "Produto não encontrado.";
        }
    }

    if (isset($_POST['excluir']) && isset($_POST['index'])) {
        unset($_SESSION['carrinho'][$_POST['index']]);
        $_SESSION['carrinho'] = array_values($_SESSION['carrinho']);
    }

    if (isset($_POST['finalizar'])) {
        $carrinho = $_SESSION['carrinho'];
        if (!empty($carrinho)) {
            $total = array_sum(array_column($carrinho, 'subtotal'));

            // Inserir venda
            $stmtVenda = $pdo->prepare("INSERT INTO vendas (id_empresa, total) VALUES (:empresa, :total)");
            $stmtVenda->execute([
                ':empresa' => $id_empresa,
                ':total' => $total
            ]);
            $id_venda = $pdo->lastInsertId();

            // Inserir itens
            $stmtItem = $pdo->prepare("INSERT INTO itens_venda 
                (id_venda, id_produto, nome_produto, preco_unitario, quantidade, subtotal)
                VALUES (:id_venda, :id_produto, :nome, :preco, :quantidade, :subtotal)");

            foreach ($carrinho as $item) {
                $stmtItem->execute([
                    ':id_venda' => $id_venda,
                    ':id_produto' => $item['id'],
                    ':nome' => $item['nome_produto'],
                    ':preco' => $item['preco'],
                    ':quantidade' => $item['quantidade'],
                    ':subtotal' => $item['subtotal']
                ]);
            }

            $_SESSION['carrinho'] = [];
            $_SESSION['recibo'] = ['id_venda' => $id_venda, 'total' => $total];
            header("Location: finalizar.php");
            exit;
        } else {
            $erro = "Carrinho vazio.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Caixa</title>
    <link rel="stylesheet" href="/system/styles/caixa.css">
    <link rel="icon" href="/img/logo_3-removebg-preview.png">
</head>
<body>
<a href="/system/painel.php"><button>Voltar</button></a>
    <h2>Caixa</h2>

    <?php if (!empty($erro)) echo "<p style='color:red;'>$erro</p>"; ?>

    <form method="POST">
        <input type="number" name="id_produto" placeholder="ID do Produto" required>
        <input type="number" name="quantidade" value="1" min="1" required>
        <button type="submit" name="adicionar">Adicionar</button>
    </form>

    <hr>

    <h3>Produtos no Carrinho</h3>
    <form method="POST">
        <table border="1" cellpadding="10">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Produto</th>
                    <th>Tipo</th>
                    <th>Validade</th>
                    <th>Preço</th>
                    <th>Qtd</th>
                    <th>Subtotal</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total = 0;
                foreach ($_SESSION['carrinho'] as $index => $produto):
                    $total += $produto['subtotal'];
                ?>
                    <tr>
                        <td><?= $produto['id'] ?></td>
                        <td><?= htmlspecialchars($produto['nome_produto']) ?></td>
                        <td><?= htmlspecialchars($produto['tipo_produto']) ?></td>
                        <td><?= $produto['data_validade'] ?></td>
                        <td>R$ <?= number_format($produto['preco'], 2, ',', '.') ?></td>
                        <td><?= $produto['quantidade'] ?></td>
                        <td>R$ <?= number_format($produto['subtotal'], 2, ',', '.') ?></td>
                        <td>
                            <button type="submit" name="excluir" value="1" onclick="document.getElementById('index').value=<?= $index ?>">Excluir</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <input type="hidden" id="index" name="index">
        <br>
        <p><strong>Total:</strong> R$ <?= number_format($total, 2, ',', '.') ?></p>
        
    </form>
    <hr>
<h3>Finalizar Compra</h3>
<form method="POST" action="finalizar.php" id="finalizar-form">
    <label for="cpf_check">
        <input type="checkbox" id="cpf_check" name="usar_cpf" onchange="toggleCPF()"> Deseja CPF na nota?
    </label>
    <input type="text" id="cpf_input" name="cpf" placeholder="Digite o CPF" style="display: none;" pattern="\d{11}">

    <select name="forma_pagamento" required>
        <option value="">Selecione o pagamento</option>
        <option value="credito">Cartão de Crédito</option>
        <option value="debito">Cartão de Débito</option>
        <option value="pix">Pix</option>
        <option value="dinheiro">Dinheiro</option>
    </select>

    <input type="hidden" name="produtos_json" id="produtos_json">
    <input type="hidden" name="valor_total" id="valor_total_input">
    <button type="submit">Finalizar Compra</button>
</form>

<script>
function toggleCPF() {
    const cpfInput = document.getElementById('cpf_input');
    cpfInput.style.display = document.getElementById('cpf_check').checked ? 'inline-block' : 'none';
}

    document.getElementById('finalizar-form').addEventListener('submit', function (e) {
        const produtos = <?= json_encode($_SESSION['carrinho']) ?>;
        const total = <?= $total ?>;

        document.getElementById('produtos_json').value = JSON.stringify(produtos);
        document.getElementById('valor_total_input').value = total;
    });


</script>

</body>
</html>
