<?php
include "conexao.php";

// Obtém os valores de 'num_pedido' e 'num_seq_item' da URL ($_GET) ou do formulário ($_POST)
$num_pedido = $_GET['num_pedido'] ??
    $_POST['num_pedido'] ?? '';
$num_seq_item = $_GET['num_seq_item'] ??
    $_POST['num_seq_item'] ?? '';

$erro = '';
$cod_item = '';
$qtd_solicitada = '';
$pre_unitario = '';

// Verifica se o formulário foi enviado (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cod_item = $_POST['cod_item'] ?? '';
    $qtd_solicitada = $_POST['qtd_solicitada'] ?? '';
    $pre_unitario = $_POST['pre_unitario'] ?? '';
} 
  elseif ($num_pedido != '' && $num_seq_item != '') {
    // Busca os dados do item do pedido para edição
    $query = "SELECT * 
                FROM item_pedido 
               WHERE num_pedido = :num_pedido 
                 AND num_seq_item = :num_seq_item";

    $params_query = ['num_pedido' => $num_pedido,'num_seq_item' => $num_seq_item];
    $row = $pdo->prepare($query);
    $row->execute($params_query);
    $item = $row->fetch(PDO::FETCH_ASSOC);

    if ($item) {
        $cod_item = $item['cod_item'];
        $qtd_solicitada = $item['qtd_solicitada'];
        $pre_unitario = $item['pre_unitario'];
    }
}

// Processa o formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Validação
    if (
        $cod_item == '' || !is_numeric($qtd_solicitada)
        || $qtd_solicitada <= 0
        || !is_numeric($pre_unitario)
        || $pre_unitario <= 0
    ) {
        $erro = "Preencha todos os campos corretamente!";
    } else {
        $query = "SELECT * 
                    FROM item 
                   WHERE cod_item = :cod_item";

        $params_query = [':cod_item' => $cod_item];
        $row = $pdo->prepare($query);
        $exe = $row->execute($params_query);
        if (!$row->fetch(PDO::FETCH_ASSOC)) {
            $erro = "O item informado não existe!";
        }
    }

    if ($erro == '') {
        if ($num_seq_item == '') {
            // Inserção: calcula próximo número de sequência
            $query = "SELECT MAX(num_seq_item) 
                          AS max_seq 
                        FROM item_pedido 
                       WHERE num_pedido = :num_pedido";

            $params_query = [':num_pedido' => $num_pedido];
            $exe = $pdo->prepare($query);           // Apenas prepare
            $exe->execute($params_query);           // Execute com os parâmetros
            $data = $exe->fetch(PDO::FETCH_ASSOC);  // Pegue os dados
            $num_seq_item = ($data['max_seq'] ?? 0) + 1;

            $sql = "INSERT INTO item_pedido(
                                num_pedido, 
                                num_seq_item, 
                                cod_item, 
                                qtd_solicitada, 
                                pre_unitario)
                         VALUES (
                                :num_pedido, 
                                :num_seq_item, 
                                :cod_item, 
                                :qtd_solicitada, 
                                :pre_unitario)";

            $params = [
                'num_pedido' => $num_pedido,
                'num_seq_item' => $num_seq_item,
                'cod_item' => $cod_item,
                'qtd_solicitada' => $qtd_solicitada,
                'pre_unitario' => $pre_unitario
            ];
        } else {
            // Atualização
            $sql = "UPDATE item_pedido
                       SET cod_item = :cod_item,
                           qtd_solicitada = :qtd_solicitada,
                           pre_unitario = :pre_unitario
                     WHERE num_pedido = :num_pedido
                       AND num_seq_item = :num_seq_item";

            $params = [
                'cod_item' => $cod_item,
                'qtd_solicitada' => $qtd_solicitada,
                'pre_unitario' => $pre_unitario,
                'num_pedido' => $num_pedido,
                'num_seq_item' => $num_seq_item
            ];
        }

        $exe = $pdo->prepare($sql);
        $row = $exe->execute($params);

        if ($exe) {
            header('Location: gerenciar_pedidos.php');
            exit;
        } else {
            $erro = "Erro ao salvar o item!";
        }
    }
}

// Busca todos os itens disponíveis
$query = "SELECT * 
            FROM item 
        ORDER BY den_item";

$row = $pdo->prepare($query);
$row->execute(); // se tirar nao aparece os items do banco de dados, nao da pra tirar 
$itens = $row->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Controlar Item de Pedido</title>
</head>

<body>

    <h2><?= $num_seq_item ? "Editar Item" : "Incluir Item" ?></h2>

    <?php if ($erro != ''): ?>
        <p style="color:red;"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="num_pedido" value="<?= htmlspecialchars($num_pedido) ?>">
        <input type="hidden" name="num_seq_item" value="<?= htmlspecialchars($num_seq_item) ?>">

        <label>Item:</label>
        <select name="cod_item" required>
            <option value="">--Selecione--</option>
            <?php foreach ($itens as $i): ?>
                <option value="<?= htmlspecialchars($i['cod_item']) ?>" <?= ($i['cod_item'] == $cod_item) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($i['den_item']) ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Quantidade:</label>
        <input type="number" step="0.001" name="qtd_solicitada" value="<?= htmlspecialchars($qtd_solicitada) ?>" required><br><br>

        <label>Preço Unitário:</label>
        <input type="number" step="0.01" name="pre_unitario" value="<?= htmlspecialchars($pre_unitario) ?>" required><br><br>

        <button type="submit">Salvar</button>
    </form>

    <p><a href="gerenciar_pedidos.php">Voltar</a></p>

</body>

</html>