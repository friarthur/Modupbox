<?php
session_start();
include_once "conexão.php";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Simulando a verificação do pagamento (substitua com a verificação real via API de pagamento)
// Substitua por uma chamada real à API de pagamento

  // ID do usuário logado



// Função para atualizar o status de pagamento
function atualizarStatusPagamento($usuarioId, $status) {
    global $pdo;
    $sql = "UPDATE empresas SET status_pagamento = :status WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":status", $status);
    $stmt->bindValue(":id", $usuarioId);
    $stmt->execute();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Compra - ModUp Box</title>
    <link rel="stylesheet" href="/system/styles/pay.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .container {
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #333;
        }

        .qrcode {
            text-align: center;
            margin-top: 20px;
        }

        .codigo-pix {
            margin-top: 20px;
            text-align: center;
        }

        input[type="text"] {
            width: 80%;
            padding: 10px;
            margin: 10px 0;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-align: center;
        }

        button {
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #2980b9;
        }

        .login-button {
            margin-top: 30px;
            text-align: center;
        }

        .btn-login {
            background-color: #2ecc71;
            color: white;
            font-size: 16px;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-login:hover {
            background-color: #27ae60;
        }

        .error-message, .success-message {
            text-align: center;
            padding: 10px;
            margin-top: 20px;
            font-size: 18px;
            border-radius: 5px;
        }

        .error-message {
            background-color: #e74c3c;
            color: white;
        }

        .success-message {
            background-color: #2ecc71;
            color: white;
        }

        .alert {
            font-size: 16px;
            text-align: center;
            color: #e74c3c;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Finalização da Assinatura</h1>
    </header>

    <div class="container">
        <h2>Pagamento via Pix</h2>
        <p>O valor da assinatura mensal é de <strong>R$ 45,00</strong>.</p>
        <p>Caso o pagamento não seja realizado até a data de vencimento, será cobrada uma multa de <strong>R$ 15,00</strong> por dia de atraso.</p>
        <p>O não pagamento pode resultar em <strong>envios de e-mails e ligações de cobrança</strong>.</p>
        <p>Para renovar o pagamento ou acessar seu histórico de transações, visite a seção <strong>“Painel de Pagamentos e Histórico”</strong> no sistema.</p>

        <div class="qrcode">
            <img src="/img/pay_valor_total.png" alt="QR Code Pix Nubank" width="300">
            <p>Escaneie o QR Code com seu aplicativo bancário para realizar o pagamento.</p>
        </div>

        <div class="codigo-pix">
            <p><strong>Código Pix (Copiar e Colar):</strong></p>
            <input type="text" id="codigoPix" value="00020126580014BR.GOV.BCB.PIX013699239cb0-bf4c-4c07-a5ca-6e11e8432feb520400005303986540545.005802BR592559.464.296 ARTHUR REIS ME6009SAO PAULO61080540900062250521bbaIlnd6wgRMbRE8wsxei6304075F" readonly>
            <button onclick="copiarCodigo()">Copiar Código</button>
        </div>

       
            <!-- Mostrar botão de Login caso o pagamento tenha sido confirmado -->
            <div class="login-button">
                <p>Após realizar o pagamento, por gentileza, envie o comprovante para nosso WhatsApp.</p>
                <a href="https://wa.me/5551981527122?text=Ol%C3%A1%2C%20queria%20mandar%20meu%20comprovante%20do%20pagamento%20do%20gerenciador%20de%20caixa."><button class="btn-login">Chamar no WhatsApp</button></a>
                

            </div>
        
            <!-- Caso contrário, exibe mensagem de aguardo -->
            <div class="alert">
                <p>Aguardando confirmação do pagamento...</p>
            </div>


    </div>

    
</body>
</html>
