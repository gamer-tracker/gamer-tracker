<?php

$host    = "localhost";
$usuario = "gt_admin"; 
$senha   = "GameTracker#2026";       
$banco   = "gametracker_db";

try {
    $conexao = new mysqli($host, $usuario, $senha, $banco);
    $conexao->set_charset("utf8mb4"); 
} catch (mysqli_sql_exception $e) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(["status" => "error", "mensagem" => "Falha crítica: O servidor de banco de dados está fora do ar."]);
    exit;
}