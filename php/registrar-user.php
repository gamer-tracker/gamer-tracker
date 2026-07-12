<?php

$host    = "localhost";
$usuario = "root"; 
$senha   = "";       
$banco   = "gametracker_db";

try {
    $conexao = new mysqli($host, $usuario, $senha, $banco);
} catch (mysqli_sql_exception $e) {
    die("❌ Conexão recusada: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome  = trim($_POST['nome_usuario']);
    $email = trim($_POST['email_usuario']);
    $senha = $_POST['senha_usuario'];

    $senhaHash = password_hash($senha, PASSWORD_BCRYPT);

    try {
        $sql = "INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("sss", $nome, $email, $senhaHash);
        $stmt->execute();
        $stmt->close();
        
        echo "<h2>Identidade forjada com sucesso!</h2>";
        echo "<br><a href='../login.html'>Ir para o Login</a>";
    } catch (mysqli_sql_exception $e) {
        if ($conexao->errno === 1062) {
            echo "❌ Esse e-mail já está registrado no sistema.";
        } else {
            echo "❌ Falha crítica: " . $e->getMessage();
        }
    }
}

$conexao->close();