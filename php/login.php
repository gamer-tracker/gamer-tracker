<?php
session_start();
require_once 'conexao.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email_usuario'] ?? '');
    $senha_digitada = $_POST['senha_usuario'] ?? '';

    try {
        $sql = "SELECT id, nome, senha FROM usuarios WHERE email = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows === 1) {
            $user = $resultado->fetch_assoc();
            if (password_verify($senha_digitada, $user['senha'])) {
                $_SESSION['usuario_id'] = $user['id'];
                $_SESSION['usuario_nome'] = $user['nome'];
                echo json_encode(["status" => "success", "mensagem" => "Acesso liberado.", "redirect" => "perfil.html"]);
                exit;
            } else {
                echo json_encode(["status" => "error", "mensagem" => "Senha incorreta."]);
                exit;
            }
        } else {
            echo json_encode(["status" => "error", "mensagem" => "Usuário não encontrado na matriz."]);
            exit;
        }
        $stmt->close();
    } catch (mysqli_sql_exception $e) {
        echo json_encode(["status" => "error", "mensagem" => "Falha crítica: " . $e->getMessage()]);
    }
}
$conexao->close();