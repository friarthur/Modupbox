<?php
include_once "conexão.php";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function validarCNPJ($cnpj) {
    $cnpj = preg_replace('/[^0-9]/', '', (string) $cnpj);
    if (strlen($cnpj) != 14) return false;

    $soma = 0;
    $multiplicador1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
    for ($i = 0; $i < 12; $i++) {
        $soma += $cnpj[$i] * $multiplicador1[$i];
    }
    $resto = $soma % 11;
    $digito1 = ($resto < 2) ? 0 : 11 - $resto;

    $soma = 0;
    $multiplicador2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
    for ($i = 0; $i < 13; $i++) {
        $soma += $cnpj[$i] * $multiplicador2[$i];
    }
    $resto = $soma % 11;
    $digito2 = ($resto < 2) ? 0 : 11 - $resto;

    return $cnpj[12] == $digito1 && $cnpj[13] == $digito2;
}

$alertMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $cnpj = trim($_POST['cnpj']);
    $telefone = trim($_POST['telefone']);
    $senha = trim($_POST['senha']);
    $nome_loja = trim($_POST['nome_loja']);
    $qtd_usuarios = trim($_POST['qtd_usuarios']);
    $endereco = trim($_POST['endereco']);

    if (empty($nome) || empty($cnpj) || empty($telefone) || empty($senha) || empty($nome_loja) || empty($qtd_usuarios) || empty($endereco)) {
        $alertMessage = "Por favor, preencha todos os campos!";
    } elseif (!validarCNPJ($cnpj)) {
        $alertMessage = "CNPJ inválido!";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM empresas WHERE cnpj = :cnpj");
        $stmt->bindParam(':cnpj', $cnpj);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $alertMessage = "Este CNPJ já está cadastrado!";
        } else {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $sql = "INSERT INTO empresas (nome, cnpj, senha, nome_loja, qtd_usuarios, endereco, telefone, data_cadastro) 
                    VALUES (:nome, :cnpj, :senha, :nome_loja, :qtd_usuarios, :endereco, :telefone, :data_cadastro)";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':cnpj', $cnpj);
            $stmt->bindParam(':senha', $senhaHash);
            $stmt->bindParam(':nome_loja', $nome_loja);
            $stmt->bindParam(':qtd_usuarios', $qtd_usuarios);
            $stmt->bindParam(':endereco', $endereco);
            $stmt->bindParam(':telefone', $telefone);
            $dataCadastro = date('Y-m-d H:i:s');
            $stmt->bindParam(':data_cadastro', $dataCadastro);

            if ($stmt->execute()) {
                $empresas_id = $pdo->lastInsertId();

                $stmt_pagamento = $pdo->prepare("
                    INSERT INTO historico_pagamentos (empresas_id, valor_total, forma_pagamento, detalhes, data_pagamento)
                    VALUES (:empresas_id, :valor_total, :forma_pagamento, :detalhes, :data_pagamento)
                ");
                $stmt_pagamento->execute([
                    'empresas_id' => $empresas_id,
                    'valor_total' => 50.00,
                    'forma_pagamento' => 'Aguardando',
                    'detalhes' => 'Cadastro inicial - pagamento pendente',
                    'data_pagamento' => date('Y-m-d H:i:s')
                ]);

                // Redireciona para a página de pagamento
                header("Location: /system/pay.php");
                exit;
            } else {
                $alertMessage = "Erro ao registrar empresa!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - ModUp Box</title>
    <link rel="stylesheet" href="/system/styles/inicial.css">
    <link rel="icon" href="/img/logo_3-removebg-preview.png">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script>
        <?php if (!empty($alertMessage)) { ?>
            window.onload = function() {
                alert("<?php echo $alertMessage; ?>");
            };
        <?php } ?>
    </script>
</head>
<body>
    <header>
        <h1>Cadastro ModUp Box</h1>
        <p>Preencha o formulário abaixo para cadastrar sua empresa.</p>
    </header>
    
    <div class="card">
        <h2>Cadastro</h2>
        <form method="POST">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" required>
            
            <label for="cnpj">CNPJ:</label>
            <input type="text" id="cnpj" name="cnpj" required>

            <label for="telefone">Número:</label>
            <input type="text" id="telefone" name="telefone" required>
            
            <label for="nome_loja">Nome da Loja:</label>
            <input type="text" id="nome_loja" name="nome_loja" required>
            
            <label for="qtd_usuarios">Quantidade de caixas:</label>
            <input type="number" id="qtd_usuarios" name="qtd_usuarios" required>
            
            <label for="endereco">Endereço:</label>
            <input type="text" id="endereco" name="endereco" required>
            
            <label for="senha">Criar Senha:</label>
            <input type="password" id="senha" name="senha" required>
            
            <a href=" /pay.php"><button type="submit">Finalizar o cadastro!</button></a>
        </form>
        <p>Já tem uma conta? <a href="login.php">Faça login</a></p>
        <p>Deseja voltar? <a href="/index.php">Ir para Página principal</a></p>
    </div>

    <footer class="footer">
        <p>&copy; 2025 Mod-Up Tecnologia. Todos os direitos reservados.</p>
        <p>CNPJ: 59.464.296/0001-44</p>
        <div class="social-links">
            <a href="https://www.linkedin.com/in/mod-up-tecnologia-98905434b/" target="_blank"><i class="fab fa-linkedin"></i> LinkedIn</a> |
            <a href="https://www.facebook.com/profile.php?id=61572602736734" target="_blank"><i class="fab fa-facebook"></i> Facebook</a> |
            <a href="https://www.instagram.com/modup_tech/" target="_blank"><i class="fab fa-instagram"></i> Instagram</a> |
            <a href="https://wa.me/+5551981527122" target="_blank"><i class="fab fa-whatsapp"></i> WhatsApp</a> |
            <a href="mailto:moduptecnologia@gmail.com" target="_blank"><i class="fas fa-envelope"></i> Email</a> |
            <a href="https://www.youtube.com/@arthurreis8923" target="_blank"><i class="fab fa-youtube"></i> YouTube</a> |
            <a href="https://www.tiktok.com/@moduptech" target="_blank"><i class="fab fa-tiktok"></i> TikTok</a>
        </div>
    </footer>
</body>
</html>
