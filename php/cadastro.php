<?php

$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "gametracker_db";

try {
    $conexao = new mysqli($host, $usuario, $senha, $banco);
} catch (mysqli_sql_exception $e) {
    die("❌ Falha na conexão com o banco de dados: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome_jogo = trim($_POST['nome_jogo'] ?? 'Jogo Sem Nome');
    $status   = trim($_POST['status_jogo'] ?? 'Jogando');

    $nota      = isset($_POST['nota_jogo']) && $_POST['nota_jogo'] !== '' ? (int)$_POST['nota_jogo'] : null;
    $review    = trim($_POST['review_jogo'] ?? '');

    try {
        $sql = "INSERT INTO jogos (nome, status_jogo, nota, review) VALUES (?, ?, ?, ?)";
        $stmt = $conexao->prepare($sql);

        $stmt->bind_param("ssis", $nome_jogo, $status, $nota, $review);

        $stmt->execute();

        echo "<h2>🎮 Jogo salvo no MySQL com sucesso!</h2>";
        echo "<p>O jogo <strong>" . htmlspecialchars($nome_jogo) . "</strong> foi guardado no banco de dados.</p>";
        echo "<br><a href='../index.html'>Voltar para o Menu</a>";

        $stmt->close();
    } catch (mysqli_sql_exception $e) {
        echo "<h2>❌ Erro ao salvar no banco de dados:</h2> " . $e->getMessage();
    }
} else {
    echo "<h2>Acesso negado. Por favor, utilize o formulário.</h2>";
}

$conexao->close();