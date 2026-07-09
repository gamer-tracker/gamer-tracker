<?php
$host    = "localhost";
$usuario = "root"; 
$senha   = "";       
$banco   = "gametracker_db";

try {
    $conexao = new mysqli($host, $usuario, $senha, $banco);
} catch (mysqli_sql_exception $e) {
    die ("❌ Falha na conexão com o banco de dados: " . $e->getMessage());
}

try {
    $sql = "SELECT id, nome, status_jogo, nota, review FROM jogos ORDER BY data_cadastro DESC";
    $resultado = $conexao->query($sql);

    $jogos = $resultado->fetch_all(MYSQLI_ASSOC);
} catch (mysqli_sql_exception $e) {
    die("❌ Eroo ao buscar os jogos: " . $e->getMessage());
}

$conexao->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-widh, initial-scale=1.0">
    <title>Minha Biblioteca de Jogos 🎮</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <header>
        <h1>🎮 Game Tracker</h1>
        <nav>
            <a href="../index.html">Início / Cadastrar</a>
            <a href="listar.php">Minha Biblioteca</a>
        </nav>
    </header>

    <main>
        <h2>Sua Estante Virtual</h2>

        <?php if (empty($jogos)): ?>
            <p>Nenhum jogo cadastrado ainda. Que tal adicionar o seu primeiro "save"?</p>
            <?php else: ?>
                <div class="lista-jogos">
                    <?php foreach ($jogos as $jogo): ?>
                    <div class="card-jogo" style="border: 1px solid #ccc; padding: 15px; margin-bottom: 15px; border-radius: 8px;">
                        <h3><?= htmlspecialchars($jogo['nome']) ?></h3>
                        
                        <p><strong>Status:</strong> <?= htmlspecialchars($jogo['status_jogo']) ?></p>
                        
                        <p><strong>Nota:</strong> <?= $jogo['nota'] !== null ? $jogo['nota'] . "/10" : "Sem nota" ?></p>
                        
                        <?php if (!empty($jogo['review'])): ?>
                            <p><strong>Review:</strong> <em>"<?= htmlspecialchars($jogo['review']) ?>"</em></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

</body>
</html>