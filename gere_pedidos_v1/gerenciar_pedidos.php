<?php
include("conexao.php");

// Query para pegar todos os clientes
$sql_clientes = "SELECT *  
                   FROM clientes 
               ORDER BY cod_cliente";

$exe = $pdo->prepare($sql_clientes);  // Prepara a query
$row = $exe->execute();               // Executa (sem parâmetros)
$clientes = $exe->fetchAll(PDO::FETCH_ASSOC);  // Pega os resultados
?>

<h2>
    <div style="text-align: center;">GERENCIAR PEDIDOS</div>
</h2>
<div style="text-align: center;">
    <a href="controlar_pedido.php">Incluir Pedido</a><br>
    <a href="gerenciar_usuario.php">Incluir um novo usuario</a><br>
    <a href="adicionar_itens.php">Adicionar um produto</a>
</div>

<?php foreach ($clientes as $cliente): ?>

    <?php
    // Busca os pedidos do cliente atual
    $query = "SELECT * 
                FROM pedido 
               WHERE cod_cliente = :cod_cliente";

    $params_query = [':cod_cliente' => $cliente['cod_cliente']];
    $row = $pdo->prepare($query);     // preparamos a query
    $exe = $row->execute($params_query); // executamos com os parâmetros

    $pedidos = $row->fetchAll(PDO::FETCH_ASSOC); // pegamos os pedidos

    if (count($pedidos) === 0) {
        continue; // pula para o próximo cliente se não houver pedidos
    }
    ?>

    <div style="text-align: center;">
        <h1>Cliente: <?= htmlspecialchars($cliente['nom_cliente']); ?></h1>
    </div>

    <?php
    // Itera sobre os pedidos do cliente
    foreach ($pedidos as $pedido):
    ?>
        <div style="text-align: center;">
            <!-- Exibe informações do pedido -->
            <br>
            <b>Pedido:</b> <?= $pedido['num_pedido']; ?>
            <a href="controlar_pedido.php?num_pedido=<?= $pedido['num_pedido']; ?>">Modificar Pedido</a>
            <a href="excluir_pedido.php?num_pedido=<?= $pedido['num_pedido']; ?>">Excluir Pedido</a>
            <a href="controlar_item_pedido.php?num_pedido=<?= $pedido['num_pedido']; ?>">Incluir Itens</a>
            <br><br>
        </div>

        <table border="1" width="600" style="margin:0 auto; text-align: center;">
            <tr>
                <th>Itens</th>
                <th>Qtde</th>
                <th>Preço</th>
                <th>Total</th>
                <th>Ação</th>
            </tr>
            <?php
            $num_pedido = $pedido['num_pedido'];

            // Consulta SQL para buscar os itens do pedido atual
            $sql_itens = "SELECT ip.*, i.den_item
                            FROM item_pedido AS ip
                            JOIN item AS i 
                              ON ip.cod_item = i.cod_item
                           WHERE ip.num_pedido = :num_pedido";

            $params_query = [':num_pedido' => $num_pedido];
            $row = $pdo->prepare($sql_itens);      // só prepara a query
            $exe = $row->execute($params_query);   // executa passando os parâmetros
            $total_pedido = 0;
            $cor = true;

            if ($row->rowCount() === 0) {
                echo "<tr><td colspan='5'>Nenhum item encontrado para este pedido.</td></tr>";
            } else {
                while ($item = $row->fetch(PDO::FETCH_ASSOC)) {
                    $total_item = $item['qtd_solicitada'] * $item['pre_unitario'];
                    $total_pedido += $total_item;

                    echo $cor ? "<tr style='background:#eee'>" : "<tr>";
                    $cor = !$cor;

                    echo "<td>" . htmlspecialchars($item['den_item']) . "</td>";
                    echo "<td>" . htmlspecialchars($item['qtd_solicitada']) . "</td>";
                    echo "<td>R$ " . number_format($item['pre_unitario'], 2, ",", ".") . "</td>";
                    echo "<td>R$ " . number_format($total_item, 2, ",", ".") . "</td>";
                    echo "<td>
                            <a href='controlar_item_pedido.php?num_pedido=" . $item['num_pedido'] . "&num_seq_item=" . $item['num_seq_item'] . "'>Modificar</a> 
                          <a href='excluir_item_pedido.php?num_pedido=" . $item['num_pedido'] . "&num_seq_item=" . $item['num_seq_item'] . "'>Excluir</a>
                     </td>";
                }
            }

            echo "<tr>
                    <td colspan='3'><b>TOTAL</b></td>
                    <td colspan='2'><b>R$ " . number_format($total_pedido, 2, ",", ".") . "</b></td>
                  </tr>";
            ?>
        </table>
    <?php endforeach; ?>
<?php endforeach; ?>