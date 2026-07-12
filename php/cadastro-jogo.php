<?php

session_start();

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(["status" => "error", "mensagem" => "Acesso negado. Faça login para salvar jogos."]);
    exit;
}

$host    = "localhost";
$usuario = "root"; 
$senha   = "";       
$banco   = "gametracker_db";

try {
    $conexao = new mysqli($host, $usuario, $senha, $banco);
} catch (mysqli_sql_exception $e) {
    echo json_encode(["status" => "error", "mensagem" => "Falha na conexão com o banco de dados."]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario_id = $_SESSION['usuario_id'];
    $nome_jogo  = trim($_POST['nome_jogo'] ?? 'Jogo Sem Nome');
    $status     = trim($_POST['status_jogo'] ?? 'Jogando');
    $nota       = isset($_POST['nota_jogo']) && $_POST['nota_jogo'] !== '' ? (int)$_POST['nota_jogo'] : null;
    $review     = trim($_POST['review_jogo'] ?? '');

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
            $ano_lancamento = substr($jogoInfo['released'] ?? 'N/A', 0, 4);
            
            if (!empty($jogoInfo['genres'])) {
                $genero = $jogoInfo['genres'][0]['name'];
            }
        }
    }

    try {
        $sql = "INSERT INTO jogos (nome, status_jogo, nota, review, genero, ano_lancamento, usuario_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ssisssi", $nome_jogo, $status, $nota, $review, $genero, $ano_lancamento, $usuario_id);
        $stmt->execute();
        $stmt->close();
        
        echo json_encode([
            "status" => "success",
            "mensagem" => "Jogo salvo com sucesso!",
            "dados_adicionais" => [
                "genero" => $genero,
                "ano_lancamento" => $ano_lancamento
            ]
        ]);
        
    } catch (mysqli_sql_exception $e) {
        echo json_encode(["status" => "error", "mensagem" => "Erro ao salvar no banco: " . $e->getMessage()]);
    }
}
$conexao->close();