<?php
include("conexao.php");

$mensagem = "";
$modo_edicao = false;
$cod_cliente_editar = "";
$nom_cliente_editar = "";

if (isset($_GET['excluir'])) {

    $cod_cliente = $_GET['excluir'];

    $query = "SELECT COUNT(*) 
                FROM pedido 
               WHERE cod_cliente = :cod_cliente";
    // Verifica se cliente possui pedidos

    $params_query = [':cod_cliente' => $cod_cliente];
    $exe = $pdo->prepare($query);
    $exe->execute($params_query);
    $row = $exe->fetchColumn();

    if ($row > 0) {
        $mensagem = "<p style='color:red;'>Não é possível excluir. Cliente possui pedidos.</p>";
    } else {

        $query = ("DELETE
                     FROM clientes 
                    WHERE cod_cliente = :cod_cliente");

        $params_query = [':cod_cliente' => $cod_cliente];
        $exe = $pdo->prepare($query);
        $sucesso = $exe->execute($params_query);

        if ($sucesso) {
            $mensagem = "<p style='color:green;'>Cliente excluído com sucesso!</p>";
        } else {
            $mensagem = "<p style='color:red;'>Erro ao excluir cliente.</p>";
        }
    }
}

if (isset($_GET['editar'])) {
    $cod_cliente_editar = $_GET['editar'];

    $query = "SELECT * 
                FROM clientes
               WHERE cod_cliente = :cod_cliente";

    $params_query = [':cod_cliente' => $cod_cliente_editar];
    $exe = $pdo->prepare($query);
    $exe->execute($params_query);
    $cliente = $exe->fetch(PDO::FETCH_ASSOC);

    if ($cliente) {
        $modo_edicao = true;
        $nom_cliente_editar = $cliente['nom_cliente'];
    }
}


if (isset($_POST['salvar_cliente'])) {

    $nom_cliente = trim($_POST['nom_cliente'] ?? '');
    $cod_cliente = $_POST['cod_cliente'] ?? null;

    if ($nom_cliente) {

        if ($cod_cliente) {

            // UPDATE
            $query = "UPDATE clientes
                         SET nom_cliente = :nom_cliente
                       WHERE cod_cliente = :cod_cliente";

            $exe = $pdo->prepare($query);
            $exe->execute([
                ':nom_cliente' => $nom_cliente,
                ':cod_cliente' => $cod_cliente
            ]);

            $mensagem = "<p style='color:green;'>Cliente atualizado!</p>";
            $modo_edicao = false;
            $cod_cliente_editar = "";
            $nom_cliente_editar = "";
        } else {

            // INSERT
            $query = "SELECT MAX(cod_cliente) FROM clientes";
            $exe = $pdo->prepare($query);
            $exe->execute();
            $max = $exe->fetchColumn();

            $cod_cliente = $max ? $max + 1 : 1;

            $query = "INSERT INTO clientes (cod_cliente, nom_cliente)
                      VALUES (:cod_cliente, :nom_cliente)";

            $exe = $pdo->prepare($query);
            $exe->execute([
                ':cod_cliente' => $cod_cliente,
                ':nom_cliente' => $nom_cliente
            ]);

            $mensagem = "<p style='color:green;'>Cliente adicionado!</p>";
        }
    } else {
        $mensagem = "<p style='color:red;'>Preencha o nome do cliente!</p>";
    }
}
?>

<h2>Adicionar Cliente</h2>

<?= $mensagem ?>

<form method="post">

    <input type="hidden" name="cod_cliente" value="<?= $cod_cliente_editar ?>">

    Nome do Cliente:
    <input type="text" name="nom_cliente" value="<?= htmlspecialchars($nom_cliente_editar) ?>" required>

    <br><br>

    <button type="submit" name="salvar_cliente">
        <?= $modo_edicao ? "Atualizar" : "Adicionar" ?>
    </button>

</form>

<hr>

<h2>Lista de Clientes</h2>

<table border="1" cellpadding="5">
    <tr>
        <th>Código</th>
        <th>Nome</th>
        <th>Ações</th>
    </tr>

    <?php
    $query = "SELECT * 
                FROM clientes 
            ORDER BY cod_cliente";

    $clientes = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
    foreach ($clientes as $cliente) {

        echo "<tr>";

        echo "<td>" . htmlspecialchars($cliente['cod_cliente']) . "</td>";

        echo "<td>" . htmlspecialchars($cliente['nom_cliente']) . "</td>";

        echo "<td>
            <a href='?editar=" . $cliente['cod_cliente'] . "'>Editar</a> |
            <a href='?excluir=" . $cliente['cod_cliente'] . "'
                onclick=\"return confirm('Tem certeza que deseja excluir este cliente?')\">
                Excluir
            </a>
          </td>";

        echo "</tr>";
    }
    ?>

</table>

<br><br>
<a href="gerenciar_pedidos.php">Voltar</a>