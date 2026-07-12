<?php

session_start();
if (!isset($_SESSION['usuario_id'])) {
    die("Acesso negado. Você não está logado no sistema.");
}

$host    = "localhost";
$usuario = "root"; 
$senha   = "";       
$banco   = "gametracker_db";

try {
    $conexao = new mysqli($host, $usuario, $senha, $banco);
} catch (mysqli_sql_exception $e) {
    die(" Falha na conexão: " . $e->getMessage());
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    try {
        $usuario_id = $_SESSION['usuario_id'];
        $sql = "DELETE FROM jogos WHERE id = ? AND usuario_id = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ii", $id, $usuario_id);
        $stmt->execute();
        $stmt->close();

        echo "<h2> Jogo excluído com sucesso !</h2>";
        echo "<p>Espero que tenha se divertido jogando.</p>";
        echo "<br><a href='listar.php'>Voltar para a Biblioteca</a>";
        exit();
   
    } catch (mysqli_sql_exception $e) {
        echo " Erro ao excluir jogo: " . $e->getMessage();
    }
} else {
    echo " ID inválido para exclusão.";
}

$conexao->close();
?>