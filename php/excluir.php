<?php
declare(strict_types=1);

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
        $sql = "DELETE FROM jogos WHERE id = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();


        // mensagemzinha para quando um jogo for excuido ;)
        echo "<h2> Jogo excluído com sucesso !</h2>";
        echo "<p>Espero que tenha se divertido jogando.</p>";
        echo "<br><a href='listar.php'>Voltar para a Biblioteca</a>";
        exit();
        //caso queiram tirar dps é so apagar e deixar esses aqui:
        //header("Location: listar.php");
        //exit();
    } catch (mysqli_sql_exception $e) {
        echo " Erro ao excluir jogo: " . $e->getMessage();
    }
} else {
    echo " ID inválido para exclusão.";
}

$conexao->close();
?>