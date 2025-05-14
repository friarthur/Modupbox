<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once "conexãoSecrets.php";
session_start();


// Verifica se é admin
if (!isset($_SESSION['usuario']) || !isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: /system/Secretsusers/loginSecrets.php');
    exit;
}

//busca de id
$dadosEmpresa = null;
if (isset($_GET['id_busca'])) {
    $idBuscado = intval($_GET['id_busca']);
    $stmt = $conn->prepare("SELECT * FROM empresas WHERE id = ?");
    $stmt->bind_param("i", $idBuscado);
    $stmt->execute();
    $result = $stmt->get_result();
    $dadosEmpresa = $result->fetch_assoc();
}


?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Usuários - ModUp Box</title>
    <link rel="stylesheet" href="/system/Secretsusers/css/gerencuser.css">
    <link rel="icon" href="/img/logo_3-removebg-preview.png">
    <style>
       body {
    font-family: Arial, sans-serif;
    padding: 20px;
}
h1 {
    margin-top: 40px;
    color: #333;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
    margin-bottom: 50px;
}
table, th, td {
    border: 1px solid #ccc;
}
th {
    background-color: #f2f2f2;
}
th, td {
    padding: 10px;
    text-align: left;
}
td a {
    color: #007BFF;
    text-decoration: none;
    margin-right: 10px;
}
td a:hover {
    text-decoration: underline;
}
.voltar-btn {
display: inline-block;
margin-bottom: 20px;
padding: 10px 20px;
background-color: #007BFF;
color: white;
text-decoration: none;
border-radius: 5px;
transition: background-color 0.2s;
}
.voltar-btn:hover {
background-color: #0056b3;
}
h2 {
    margin-bottom: 20px;
    color: #333;
  }
  
  /* Form principal */
  form {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    max-width: 600px;
    margin-bottom: 40px;
  }
  
  /* Inputs e labels */
  form label {
    display: block;
    margin-bottom: 6px;
    font-weight: bold;
    color: #444;
  }
  
  form input[type="text"],
  form input[type="number"],
  form input[type="datetime-local"],
  form textarea,
  form select {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 15px;
    transition: border-color 0.3s;
  }
  
  form input:focus,
  form textarea:focus,
  form select:focus {
    border-color: #3b82f6;
    outline: none;
  }
  
  /* Botões */
  form button {
    background-color: #3b82f6;
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 8px;
    font-size: 16px;
    cursor: pointer;
    transition: background 0.3s;
  }
  
  form button:hover {
    background-color: #2563eb;
  }
  
  /* Mensagem de erro */
  p[style*="color:red"] {
    color: red;
    margin-top: 10px;
  }
    </style>
</head>
<body>
<a href="/system/Secretsusers/painelSecrets.php" class="voltar-btn">⬅ Voltar para o Painel Principal</a>

<h1>Administradores</h1>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Usuário</th>
            
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sql = "SELECT * FROM administradores";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()):
        ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['usuario']) ?></td>
                
                <td>
                    <a href="#">Editar</a>
                    <a href="#">Excluir</a>
                </td>
            </tr>
        <?php endwhile; ?>
      
    </tbody>
</table>

<h1>Clientes (Empresas)</h1>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Nome da Loja</th>
            
            <th>CNPJ</th>
            <th>Telefone</th>
            <th>Qtd. Usuários</th>
            <th>Endereço</th>
            <th>Status Pagamento</th>
            <th>Data de Pagamento</th>
            <th>Data de Cadastro</th>
          
        </tr>
    </thead>
    <tbody>
        <?php
        $sql = "SELECT * FROM empresas";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()):
        ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['nome']) ?></td>
                <td><?= htmlspecialchars($row['nome_loja']) ?></td>
              
                <td><?= htmlspecialchars($row['cnpj']) ?></td>
                <td><?= htmlspecialchars($row['telefone']) ?></td>
                <td><?= $row['qtd_usuarios'] ?></td>
                <td><?= nl2br(htmlspecialchars($row['endereco'])) ?></td>
                <td><?= htmlspecialchars($row['status_pagamento']) ?></td>
                <td><?= $row['data_pagamento'] ?></td>
                <td><?= $row['data_cadastro'] ?></td>
               

                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<h2>Editar Empresa</h2>

<form method="GET" style="margin-bottom: 30px;">
    <label for="id_busca">ID da Empresa:</label>
    <input type="number" name="id_busca" id="id_busca" required>
    <button type="submit">Buscar</button>
</form>

<?php if ($dadosEmpresa): ?>
<form method="POST" action="salvar_edicao.php">
    <input type="hidden" name="id" value="<?= $dadosEmpresa['id'] ?>">

    <label>Nome:</label><br>
    <input type="text" name="nome" value="<?= htmlspecialchars($dadosEmpresa['nome']) ?>"><br>

    <label>Nome da Loja:</label><br>
    <input type="text" name="nome_loja" value="<?= htmlspecialchars($dadosEmpresa['nome_loja']) ?>"><br>

    <label>CNPJ:</label><br>
    <input type="text" name="cnpj" value="<?= htmlspecialchars($dadosEmpresa['cnpj']) ?>"><br>

    <label>Telefone:</label><br>
    <input type="text" name="telefone" value="<?= htmlspecialchars($dadosEmpresa['telefone']) ?>"><br>

    <label>Qtd. Usuários:</label><br>
    <input type="number" name="qtd_usuarios" value="<?= $dadosEmpresa['qtd_usuarios'] ?>"><br>

    <label>Endereço:</label><br>
    <textarea name="endereco"><?= htmlspecialchars($dadosEmpresa['endereco']) ?></textarea><br>

    <label>Status Pagamento:</label><br>
    <select name="status_pagamento">
        <option value="ativo" <?= $dadosEmpresa['status_pagamento'] === 'ativo' ? 'selected' : '' ?>>Ativo</option>
        <option value="inativo" <?= $dadosEmpresa['status_pagamento'] === 'inativo' ? 'selected' : '' ?>>Inativo</option>
    </select><br>

    <label>Data de Pagamento:</label><br>
    <input type="datetime-local" name="data_pagamento" value="<?= date('Y-m-d\TH:i', strtotime($dadosEmpresa['data_pagamento'])) ?>"><br>

    <label>Data de Cadastro:</label><br>
    <input type="datetime-local" name="data_cadastro" value="<?= date('Y-m-d\TH:i', strtotime($dadosEmpresa['data_cadastro'])) ?>"><br><br>

    <button type="submit">Salvar Alterações</button>
</form>
<?php elseif (isset($_GET['id_busca'])): ?>
    <p style="color:red;">Empresa com ID <?= htmlspecialchars($_GET['id_busca']) ?> não encontrada.</p>
<?php endif; ?>

</body>
</html>