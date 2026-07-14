<?php
session_start();
require_once 'conexao.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(["status" => "error", "mensagem" => "Acesso negado."]);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $id = (int)$_GET['id'];
    $stmt = $conexao->prepare("SELECT id, nome, status_jogo, nota, review FROM jogos WHERE id = ? AND usuario_id = ?");
    $stmt->bind_param("ii", $id, $usuario_id);
    $stmt->execute();
    $jogo = $stmt->get_result()->fetch_assoc();
    echo json_encode(["status" => "success", "data" => $jogo]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = (int)$_POST['id_jogo'];
    $status = $_POST['status_jogo'];
    $nota = (int)$_POST['nota_jogo'];
    $review = $_POST['review_jogo'];

    $stmt = $conexao->prepare("UPDATE jogos SET status_jogo = ?, nota = ?, review = ? WHERE id = ? AND usuario_id = ?");
    $stmt->bind_param("sisii", $status, $nota, $review, $id, $usuario_id);
    
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "mensagem" => "Jogo atualizado!", "redirect" => "biblioteca.html"]);
    } else {
        echo json_encode(["status" => "error", "mensagem" => "Erro ao atualizar."]);
    }
}