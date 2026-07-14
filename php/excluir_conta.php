<?php
session_start();
require_once 'conexao.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(["status" => "error", "mensagem" => "Acesso negado."]);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

try {
    $stmt = $conexao->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $usuario_id);
    
    if ($stmt->execute()) {
        session_unset();
        session_destroy();
        echo json_encode(["status" => "success", "mensagem" => "Conta excluída com sucesso.", "redirect" => "index.html"]);
    } else {
        echo json_encode(["status" => "error", "mensagem" => "Erro ao excluir conta."]);
    }
    $stmt->close();
} catch (mysqli_sql_exception $e) {
    echo json_encode(["status" => "error", "mensagem" => "Erro no banco: " . $e->getMessage()]);
}

$conexao->close();