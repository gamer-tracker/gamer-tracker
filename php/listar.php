<?php

session_start();

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(["status" => "error", "mensagem" => "Acesso negado. Você não está logado na matriz."]);
    exit;
}

$host    = "localhost";
$usuario = "root"; 
$senha   = "";       
$banco   = "gametracker_db";

try {
    $conexao = new mysqli($host, $usuario, $senha, $banco);
} catch (mysqli_sql_exception $e) {
    echo json_encode(["status" => "error", "mensagem" => "Falha crítica na conexão com o banco."]);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$termo_busca = trim($_GET['busca'] ?? '');
$jogos = [];

try {
    if ($termo_busca !== '') {
        $sql = "SELECT id, nome, status_jogo, nota, review, genero, ano_lancamento FROM jogos WHERE nome LIKE ? AND usuario_id = ? ORDER BY data_cadastro DESC";
        $stmt = $conexao->prepare($sql);
        
        $parametro = "%" . $termo_busca . "%";
        $stmt->bind_param("si", $parametro, $usuario_id);
        
        $stmt->execute();
        $resultado = $stmt->get_result();
        $jogos = $resultado->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    } else {
        $sql = "SELECT id, nome, status_jogo, nota, review, genero, ano_lancamento FROM jogos WHERE usuario_id = ? ORDER BY data_cadastro DESC";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $jogos = $resultado->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }

    echo json_encode([
        "status" => "success",
        "total_jogos" => count($jogos),
        "data" => $jogos
    ]);

} catch (mysqli_sql_exception $e) {
    echo json_encode(["status" => "error", "mensagem" => "Erro ao buscar os jogos: " . $e->getMessage()]);
}

$conexao->close();