<?php
$host = 'localhost'; 
$dbname = 'u873784516_modup_box'; 
$username = 'u873784516_modupbox'; 
$password = '150813Fr@$'; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    echo "Erro na conexão: " . $e->getMessage();
    die(); // Encerra o script se a conexão falhar
}

// Coloque o código de consulta aqui
$stmt = $pdo->prepare("SELECT id FROM empresas WHERE cnpj = :cnpj");
?>
