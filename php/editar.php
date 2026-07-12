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
    die("❌ Falha na conexão: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id     = (int)$_POST['id_jogo'];
    $status = trim($_POST['status_jogo']);
    $nota   = $_POST['nota_jogo'] !== '' ? (int)$_POST['nota_jogo'] : null;
    $review = trim($_POST['review_jogo']);

    try {
        $usuario_id = $_SESSION['usuario_id'];
        $sql = "UPDATE jogos SET status_jogo = ?, nota = ?, review = ? WHERE id = ? AND usuario_id = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("sisii", $status, $nota, $review, $id, $usuario_id);
        $stmt->execute();
        $stmt->close();

        header("Location: listar.php");
        exit;
    } catch (mysqli_sql_exception $e) {
        echo "❌ Erro ao atualizar: " . $e->getMessage();
    }
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$jogo = null;

if ($id > 0) {
    $sql = "SELECT id, nome, status_jogo, nota, review FROM jogos WHERE id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $jogo = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

if (!$jogo) {
    die("Jogo não encontrado.");
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Jogo 📝</title>
</head>
<body>
<h2>Editar Status/Avaliação de: <?= htmlspecialchars($jogo['nome']) ?></h2>

<form action="editar.php" method="POST">
    <input type="hidden" name="id_jogo" value="<?= $jogo['id'] ?>">

    <label>Status:</label>
    <select name="status_jogo">
        <option value="Jogando" <?= $jogo['status_jogo'] === 'Jogando' ? 'selected' : '' ?>>Jogando</option>
        <option value="Já zerei" <?= $jogo['status_jogo'] === 'Já zerei' ? 'selected' : '' ?>>Já zerei</option>
        <option value="Platinado" <?= $jogo['status_jogo'] === 'Platinado' ? 'selected' : '' ?>>Platinado</option>
    </select><br><br>

    <label>Nota (0 a 10):</label>
    <input type="number" name="nota_jogo" min="0" max="10" value="<?= $jogo['nota'] ?>"><br><br>

    <label>Sua Review:</label><br>
    <textarea name="review_jogo" rows="4" cols="50"><?= htmlspecialchars($jogo['review']) ?></textarea><br><br>

    <button type="submit">Salvar Alterações</button>
    <a href="listar.php">Cancelar</a>
</form>
</body>
</html>