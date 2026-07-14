<?php
session_start();
require_once 'conexao.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(["status" => "error", "mensagem" => "Não logado"]);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

try {
    // Busca os dados filtrando pelo usuário logado
    $query = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status_jogo = 'Jogando' THEN 1 ELSE 0 END) as jogando,
                SUM(CASE WHEN status_jogo = 'Já zerei' THEN 1 ELSE 0 END) as zerado,
                SUM(CASE WHEN status_jogo = 'Platinado' THEN 1 ELSE 0 END) as platinado
              FROM jogos WHERE usuario_id = ?";
              
    $stmt = $conexao->prepare($query);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $resultado = $stmt->get_result()->fetch_assoc();

    echo json_encode([
        "status" => "success",
        "data" => [
            "total" => (int)$resultado['total'],
            "jogando" => (int)$resultado['jogando'],
            "zerado" => (int)$resultado['zerado'],
            "platinado" => (int)$resultado['platinado']
        ]
    ]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "mensagem" => $e->getMessage()]);
}