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
    $nome_jogo = trim($_POST['nome_jogo'] ?? 'Jogo Sem Nome');
    $status    = trim($_POST['status_jogo'] ?? 'Jogando');
    $nota      = isset($_POST['nota_jogo']) && $_POST['nota_jogo'] !== '' ? (int)$_POST['nota_jogo'] : null;
    $review    = trim($_POST['review_jogo'] ?? '');

    $apiKey = "cf26b0e08d3c4bb1801178470ce186b1";
    $urlApi = "https://api.rawg.io/api/games?key=" . $apiKey . "&search=" . urlencode($nome_jogo);

    $genero = "Desconhecido";
    $ano_lancamento = "N/A";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $urlApi);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $resposta = curl_exec($ch);
    curl_close($ch);

    if ($resposta) {
        $dadosApi = json_decode($resposta, true);
        if (!empty($dadosApi['results'])) {
            $jogoInfo = $dadosApi['results'][0];
            $ano_lancamento = substr($jogoInfo['released'] ?? 'N/A', 0, 4); // Pega só o ano (YYYY)

            if (!empty($jogoInfo['genres'])) {
                $genero = $jogoInfo['genres'][0]['name'];
            }
        }
    }

    $usuario_id = $_SESSION['usuario_id'];

    try {
        $sql = "INSERT INTO jogos (nome, status_jogo, nota, review, genero, ano_lancamento, usuario_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ssisssi", $nome_jogo, $status, $nota, $review, $genero, $ano_lancamento, $usuario_id);
        $stmt->execute();
        $stmt->close();

        echo "<h2>🎮 Jogo salvo!</h2>";
        echo "<p>Gênero: $genero | Ano: $ano_lancamento</p>";
        echo "<br><a href='../index.html'>Voltar</a>";
    } catch (mysqli_sql_exception $e) {
        echo "❌ Erro ao salvar: " . $e->getMessage();
    }
}
$conexao->close();