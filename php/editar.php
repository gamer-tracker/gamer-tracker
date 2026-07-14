<?php
session_start();
require_once 'conexao.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(["status" => "error", "mensagem" => "Acesso negado. Você precisa estar logado na matriz."]);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    if (!isset($_GET['id'])) {
        echo json_encode(["status" => "error", "mensagem" => "Faltou enviar o ID do jogo."]);
        exit;
    }

    $id = (int)$_GET['id'];

    try {
        $sql = "SELECT id, nome, status_jogo, nota, review FROM jogos WHERE id = ? AND usuario_id = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ii", $id, $usuario_id);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $jogo = $resultado->fetch_assoc();
            echo json_encode(["status" => "success", "data" => $jogo]);
        } else {
            echo json_encode(["status" => "error", "mensagem" => "Jogo não encontrado na sua estante."]);
        }
        $stmt->close();
    } catch (Exception $e) {
         echo json_encode(["status" => "error", "mensagem" => "Erro interno: " . $e->getMessage()]);
    }
}

elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $id = isset($_POST['id_jogo']) ? (int)$_POST['id_jogo'] : 0;
    
    $status = trim($_POST['status_jogo'] ?? '');

    $nota = isset($_POST['nota_jogo']) && $_POST['nota_jogo'] !== '' ? (int)$_POST['nota_jogo'] : null;
    if ($nota != null && ($nota < 1 || $nota > 10)) {
        echo json_encode(["status" => "error", "mensagem" => "A nota deve ser entre 1 e 10."]);
        exit;
    }

    $review = trim($_POST['review_jogo'] ?? '');

    if (mb_strlen($review, 'UTF-8') > 500) {
        echo json_encode(["status" => "error", "mensagem" => "Calma aí, Shakespeare! O review deve ter no máximo 500 caracteres."]);
        exit;
    }

    if ($id === 0) {
         echo json_encode(["status" => "error", "mensagem" => "ID do jogo não foi enviado no formulário."]);
         exit;
    }

    try {
        $sql = "UPDATE jogos SET status_jogo = ?, nota = ?, review = ? WHERE id = ? AND usuario_id = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("sisii", $status, $nota, $review, $id, $usuario_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode(["status" => "success", "mensagem" => "Save atualizado com sucesso!"]);
        } else {
            echo json_encode(["status" => "success", "mensagem" => "Nenhuma alteração foi necessária."]);
        }
        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "mensagem" => "Erro ao atualizar: " . $e->getMessage()]);
    }
}

$conexao->close();