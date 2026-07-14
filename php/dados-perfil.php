<?php
session_start();
require_once 'conexao.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(["status" => "error", "mensagem" => "Usuário não autenticado."]);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

try {
    $sql = "SELECT nome, email FROM usuarios WHERE id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $user = $resultado->fetch_assoc();
        echo json_encode(["status" => "success", "data" => $user]);
    } else {
        echo json_encode(["status" => "error", "mensagem" => "Dados não encontrados."]);
    }
    $stmt->close();
} catch (mysqli_sql_exception $e) {
    echo json_encode(["status" => "error", "mensagem" => "Erro na matriz: " . $e->getMessage()]);
}

$conexao->close();