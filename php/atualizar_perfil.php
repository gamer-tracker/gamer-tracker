<?php
session_start();
require_once 'conexao.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(["status" => "error", "mensagem" => "Acesso negado."]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario_id = $_SESSION['usuario_id'];
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $nova_senha = $_POST['nova_senha'] ?? '';

    try {
        if (!empty($nova_senha)) {
            $senhaHash = password_hash($nova_senha, PASSWORD_BCRYPT);
            $sql = "UPDATE usuarios SET nome = ?, email = ?, senha = ? WHERE id = ?";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("sssi", $nome, $email, $senhaHash, $usuario_id);
        } else {
            $sql = "UPDATE usuarios SET nome = ?, email = ? WHERE id = ?";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("ssi", $nome, $email, $usuario_id);
        }
        
        $stmt->execute();
        echo json_encode(["status" => "success", "mensagem" => "Perfil atualizado com sucesso!", "redirect" => "perfil.html"]);
        $stmt->close();
    } catch (mysqli_sql_exception $e) {
        echo json_encode(["status" => "error", "mensagem" => "Erro ao atualizar: " . $e->getMessage()]);
    }
}
$conexao->close();