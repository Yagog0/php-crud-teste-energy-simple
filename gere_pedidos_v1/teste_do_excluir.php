<?php
include("conexao.php");

if (!isset($_GET['num_pedido'])) {
    die("Erro: Número do pedido não foi informado.");
}

$num_pedido = (int)$_GET['num_pedido'];

try {
    // Verifica se existem itens no pedido
    $query = "SELECT COUNT(*) AS qtde_itens 
                FROM item_pedido 
               WHERE num_pedido = :num_pedido";

    $params_query = [':num_pedido' => $num_pedido];
    $exe = $pdo->prepare($query);
    $exe->execute($params_query);
    $data = $exe->fetch(PDO::FETCH_ASSOC);
    $qtde_itens = $data['qtde_itens'] ?? 0;

    if ($qtde_itens > 0) {
        echo "<script>
                alert('Não é possível apagar este pedido! Existem $qtde_itens item(s) cadastrados nele.');
                window.location.href='gerenciar_pedidos.php';
              </script>";
        exit;
    }

    // Verifica se pedido existe
    $query = "SELECT COUNT(*)  
                  AS total 
                FROM pedido 
               WHERE num_pedido = :num_pedido";

    $params_query = [':num_pedido' => $num_pedido];
    $exe = $pdo->prepare($query);
    $exe->execute($params_query);
    $data = $exe->fetch(PDO::FETCH_ASSOC);

    if ($data['total'] == 0) {
        die("Pedido não encontrado no banco.");
    }

    // Apaga o pedido
    $query = "DELETE 
                FROM pedido 
               WHERE num_pedido = :num_pedido";

    $params_query = [':num_pedido' => $num_pedido];
    $exe = $pdo->prepare($query);
    $exe->execute($params_query);

    $linhas_afetadas = $exe->rowCount();
    if ($linhas_afetadas === 0) {
        die("Nenhum pedido foi apagado. Verifique o número do pedido.");
    }

    echo "<script>
            alert('Pedido apagado com sucesso!');
            window.location.href='gerenciar_pedidos.php';
          </script>";
    exit;

} catch (PDOException $e) {
    die("Erro ao excluir o pedido: " . $e->getMessage());
}