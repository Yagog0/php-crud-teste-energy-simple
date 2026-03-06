<?php
include "conexao.php";

// 1. Buscar clientes
$query = "SELECT * 
            FROM clientes 
        ORDER BY cod_cliente";

$exe = $pdo->prepare($query);
$exe->execute();
$clientes = $exe->fetchAll(PDO::FETCH_ASSOC);

// 2. Buscar itens
$query = "SELECT * 
            FROM item 
        ORDER BY cod_item";

$exe = $pdo->prepare($query);
$exe->execute();
$itens = $exe->fetchAll(PDO::FETCH_ASSOC);

// 3. Determinar num_pedido
$num_pedido = $_GET['num_pedido'] ?? null;
$mensagem_erro = "";

// Buscar cliente do pedido existente
if (!empty($num_pedido)) {

    $prepare = "SELECT cod_cliente 
                 FROM pedido 
                WHERE num_pedido = :num_pedido";

    $params_query = [':num_pedido' => $num_pedido];
    $exe = $pdo->prepare($prepare);
    $exe->execute($params_query);
    $pedido = $exe->fetch(PDO::FETCH_ASSOC);

    if ($pedido) {
        $cod_cliente = $pedido['cod_cliente'];
    } else {
        $mensagem_erro = "Pedido não encontrado.";
    }
}

// Se for POST e pedido não existe, determinar próximo num_pedido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($num_pedido)) {

    $query = "SELECT MAX(num_pedido) 
                FROM pedido";

    $exe = $pdo->prepare($query);
    $exe->execute();
    $max_num = $exe->fetchColumn();
    $num_pedido = $max_num ? $max_num + 1 : 1;
}

// Processar inserção ou atualização
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cod_cliente = trim($_POST['cod_cliente']);
    $cod_item = trim($_POST['cod_item'] ?? '');
    $qtd_solicitada = $_POST['qtd_solicitada'] ?? '';
    $pre_unitario = $_POST['pre_unitario'] ?? '';

    if (!$cod_cliente) {
        $mensagem_erro = "Cliente obrigatório.";
    } else {
        try {
            // Atualiza ou insere pedido

            $query = "SELECT num_pedido 
                        FROM pedido 
                       WHERE num_pedido = :num_pedido";

            $params_query = [':num_pedido' => $num_pedido];
            $exe = $pdo->prepare($query);
            $exe->execute($params_query);
            $existe = $exe->fetch(PDO::FETCH_ASSOC);

            if ($existe) {

                $query = "UPDATE pedido 
                             SET cod_cliente = :cod_cliente 
                           WHERE num_pedido = :num_pedido";

                $params_query = [':cod_cliente' => $cod_cliente, ':num_pedido' => $num_pedido];
                $exe = $pdo->prepare($query);
                $exe->execute($params_query);
            } else {
                $query = "INSERT INTO 
                               pedido (
                                     num_pedido, 
                                     cod_cliente) 
                              VALUES 
                                     (:num_pedido, 
                                      :cod_cliente)";

                $params_query = [':num_pedido' => $num_pedido, ':cod_cliente' => $cod_cliente];
                $exe = $pdo->prepare($query);
                $exe->execute($params_query);
            }

            // Inserir item apenas se informado
            if (!empty($cod_item) && !empty($qtd_solicitada) && !empty($pre_unitario)) {

                $query = "SELECT MAX(num_seq_item) 
                            FROM item_pedido 
                           WHERE num_pedido = :num_pedido";

                $params_query = [':num_pedido' => $num_pedido];
                $exe = $pdo->prepare($query);
                $exe->execute($params_query);
                $max_seq = $exe->fetchColumn();
                $num_seq_item = $max_seq ? $max_seq + 1 : 1;

                $pdo->exec("INSERT INTO item_pedido (
                                num_pedido,
                                num_seq_item,
                                cod_item,
                                qtd_solicitada,
                                pre_unitario
                            ) VALUES (
                                $num_pedido,
                                $num_seq_item,
                                $cod_item,
                                $qtd_solicitada,
                                $pre_unitario
                            )");
            }

            header("Location: gerenciar_pedidos.php");
            exit;
        } catch (PDOException $e) {
            $mensagem_erro = "Erro ao salvar pedido ou item: " . $e->getMessage();
        }
    }
}
?>

<h3>Controlar Pedido</h3>

<?php if (!empty($mensagem_erro)) : ?>
    <p style="color:red"><?= htmlspecialchars($mensagem_erro) ?></p>
<?php endif; ?>

<form method="post">
    <label>Cliente:</label>
    <select name="cod_cliente" required>
        <option value="">--Selecione--</option>
        <?php foreach ($clientes as $c): ?>
            <option value="<?= htmlspecialchars($c['cod_cliente']) ?>" <?= ($c['cod_cliente'] == $cod_cliente) ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['nom_cliente']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <br><br>

    <label>Item (opcional):</label>
    <select name="cod_item">
        <option value="">--Selecione--</option>
        <?php foreach ($itens as $i): ?>
            <option value="<?= htmlspecialchars($i['cod_item']) ?>">
                <?= htmlspecialchars($i['den_item']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <br><br>

    <label>Quantidade:</label>
    <input type="number" name="qtd_solicitada" step="0.001">
    <br><br>

    <label>Preço Unitário:</label>
    <input type="number" name="pre_unitario" step="0.01">
    <br><br>

    <button type="submit">Salvar Pedido</button>
</form>