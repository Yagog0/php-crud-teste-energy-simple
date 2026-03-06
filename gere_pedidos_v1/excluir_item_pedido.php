<?php
include("conexao.php");

// Verifica se os parâmetros 'num_pedido' e 'num_seq_item' foram passados via GET
if (!isset($_GET['num_pedido']) || !isset($_GET['num_seq_item'])) {
    die("Erro: Dados não informados.");
}

// Converte os valores para inteiro (evita SQL Injection)
$num_pedido = $_GET['num_pedido'];
$num_seq_item = $_GET['num_seq_item'];

try {
    // Cria a query DELETE diretamente
    $query = "DELETE FROM item_pedido 
                    WHERE num_pedido = :num_pedido
                      AND num_seq_item = :num_seq_item";

    $param_query = [':num_pedido' => $num_pedido, ':num_seq_item' => $num_seq_item];
    $exe = $pdo->prepare($query);
    $sucesso = $exe->execute($param_query);

    if ($sucesso == false) {
        die("Erro ao excluir o item do pedido.");
    }

    // Redireciona de volta
    header("Location: gerenciar_pedidos.php");
    exit;
} catch (PDOException $e) {
    die("Erro ao excluir o item: " . $e->getMessage());
}
