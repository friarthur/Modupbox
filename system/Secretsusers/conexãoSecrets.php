<?php
$host = 'localhost'; 
$dbname = 'u873784516_modup_box'; 
$username = 'u873784516_modupbox'; 
$password = '150813Fr@$'; 
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_errno == 0) {
    // Conectado com sucesso
} else {
    echo "Erro na conexão com o banco de dados";
    exit;
}
?>