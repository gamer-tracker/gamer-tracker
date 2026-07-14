<?php

session_start();
require_once 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email_usuario']);
    $senha_digitada = $_POST['senha_usuario'];

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

                header("Location: listar.php");
                exit;
            } else {
                echo "❌ Senha incorreta.";
            }
        } else {
            echo "❌ Usuário não encontrado na matriz.";
        }
        $stmt->close();
    } catch (mysqli_sql_exception $e) {
        echo "❌ Falha crítica: " . $e->getMessage();
    }
}
$conexao->close();