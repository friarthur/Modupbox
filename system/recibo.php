<?php
session_start();

if (!isset($_SESSION['recibo'])) {
    header("Location: caixa.php");
    exit;
}

$recibo = $_SESSION['recibo'];
unset($_SESSION['recibo']);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Recibo</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f8;
            color: #333;
            margin: 0;
            padding: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h2 {
            color: #2c3e50;
            margin-bottom: 20px;
        }

        p {
            font-size: 18px;
            margin: 10px 0;
        }

        a {
            text-decoration: none;
        }

        a button {
            background-color: #3498db;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
        }

        a button:hover {
            background-color: #2980b9;
            transform: scale(1.05);
        }

        a button:active {
            background-color: #2471a3;
            transform: scale(0.98);
        }
    </style>
</head>
<body>
    <h2>Compra Finalizada</h2>
    <p><strong>ID da Venda:</strong> <?= $recibo['id_venda'] ?></p>
    <p><strong>Total:</strong> R$ <?= number_format($recibo['total'], 2, ',', '.') ?></p>
    <a href="caixa.php"><button>Voltar ao Caixa</button></a>
</body>
</html>
