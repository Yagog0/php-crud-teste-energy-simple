<?php

$host = "127.0.0.3";
$banco = "banco";
$usuario = "root";
$senha = "12simple36";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$banco;charset=utf8", $usuario, $senha);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erro na conexão: " . $e->getMessage();
}

function showArray($array)
{
    echo "<pre>";
    print_r($array);
    echo "</pre>";
}

