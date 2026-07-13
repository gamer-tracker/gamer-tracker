<?php

header('Content-Type: application/json; charset=utf-8');

$host    = "localhost";
$usuario = "root"; 
$senha   = "";       
$banco   = "gametracker_db";

try {
    $conexao = new mysqli($host, $usuario, $senha, $banco);
} catch (mysqli_sql_exception $e) {
    echo json_encode(["status" => "error", "mensagem" => "Falha crítica de conexão: " . $e->getMessage()]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome  = trim($_POST['nome_usuario'] ?? '');
    $email = trim($_POST['email_usuario'] ?? '');
    $senha = $_POST['senha_usuario'] ?? '';

    if (empty($nome) || empty($email) || empty($senha)) {
        echo json_encode(["status" => "error", "mensagem" => "Todos os campos são obrigatórios."]);
        exit;
    }

    $senhaHash = password_hash($senha, PASSWORD_BCRYPT);

    try {
        $sql = "INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("sss", $nome, $email, $senhaHash);
        $stmt->execute();
        $stmt->close();
        
        echo json_encode(["status" => "success", "mensagem" => "Identidade forjada com sucesso!", "name" => $nome, "email" => $email]);
    } catch (mysqli_sql_exception $e) {
        if ($conexao->errno === 1062) {
            echo json_encode(["status" => "error", "mensagem" => "Esse e-mail já está registrado na base de dados."]);
        } else {
            echo json_encode(["status" => "error", "mensagem" => "Falha interna: " . $e->getMessage()]);
        }
    }
}
$conexao->close();
?>