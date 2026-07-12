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
} catch (mysqli_sql_exception $e) {
    die("❌ Erro ao buscar os jogos: " . $e->getMessage());
}

$conexao->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minha Biblioteca de Jogos 🎮</title>
</head>
<body>

<header>
    <h1>🎮 Game Tracker</h1>
    <nav>
        <a href="../index.html">Início / Cadastrar</a> |
        <a href="listar.php">Minha Biblioteca</a>
    </nav>
</header>

<main>
    <h2>Sua Estante Virtual</h2>

    <form action="listar.php" method="GET" style="margin-bottom: 20px;">
        <input type="text" name="busca" placeholder="Buscar jogo pelo nome..." value="<?= htmlspecialchars($termo_busca) ?>">
        <button type="submit">🔍 Buscar</button>
        <?php if ($termo_busca !== ''): ?>
            <a href="listar.php"><button type="button">Limpar Filtro</button></a>
        <?php endif; ?>
    </form>

    <?php if (empty($jogos)): ?>
        <p>Nenhum jogo encontrado.</p>
    <?php else: ?>
        <div class="lista-jogos">
            <?php foreach ($jogos as $jogo): ?>
                <div class="card-jogo" style="border: 1px solid #ccc; padding: 15px; margin-bottom: 15px; border-radius: 8px;">

                    <h3><?= htmlspecialchars($jogo['nome']) ?></h3>

                    <p><small><strong>Gênero:</strong> <?= htmlspecialchars($jogo['genero'] ?? 'N/A') ?> | <strong>Ano:</strong> <?= htmlspecialchars($jogo['ano_lancamento'] ?? 'N/A') ?></small></p>

                    <p><strong>Status:</strong> <?= htmlspecialchars($jogo['status_jogo']) ?></p>
                    <p><strong>Nota:</strong> <?= $jogo['nota'] !== null ? $jogo['nota'] . "/10" : "Sem nota" ?></p>

                    <?php if (!empty($jogo['review'])): ?>
                        <p><strong>Review:</strong> <em>"<?= htmlspecialchars($jogo['review']) ?>"</em></p>
                    <?php endif; ?>

                    <div style="margin-top: 10px;">
                        <a href="editar.php?id=<?= $jogo['id'] ?>">✏️ Editar</a> |
                        <a href="excluir.php?id=<?= $jogo['id'] ?>" onclick="return confirm('Tem certeza que deseja apagar esse save?')">❌ Excluir</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

</body>
</html>