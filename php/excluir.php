<?php
session_start();

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(["status" => "error", "mensagem" => "Acesso negado. Você precisa estar logado para excluir."]);
    exit;
}

$host    = "localhost";
$usuario = "root"; 
$senha   = "";       
$banco   = "gametracker_db";

try {
    $conexao = new mysqli($host, $usuario, $senha, $banco);
} catch (mysqli_sql_exception $e) {
    echo json_encode(["status" => "error", "mensagem" => "Falha na conexão com a matriz."]);
    exit;
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $usuario_id = $_SESSION['usuario_id'];

    try {
        $sql = "DELETE FROM jogos WHERE id = ? AND usuario_id = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ii", $id, $usuario_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode(["status" => "success", "mensagem" => "Jogo deletado da sua estante com sucesso."]);
        } else {
            echo json_encode(["status" => "error", "mensagem" => "Jogo não encontrado ou você não tem permissão para apagá-lo."]);
        }
        
        $stmt->close();
    } catch (mysqli_sql_exception $e) {
        echo json_encode(["status" => "error", "mensagem" => "Erro ao tentar apagar o registro: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "mensagem" => "ID do jogo não foi enviado."]);
}

$conexao->close();
?>