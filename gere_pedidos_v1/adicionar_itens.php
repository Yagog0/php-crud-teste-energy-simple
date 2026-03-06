<?php
include("conexao.php");

$mensagem = "";
$modo_edicao = false;
$cod_item_editar = "";
$den_item_editar = "";

if (isset($_GET['excluir'])) {

    $cod_item = $_GET['excluir'];


    $query = "SELECT COUNT(*)
                FROM item_pedido
                WHERE cod_item = :cod_item";


    $params_query = [':cod_item' => $cod_item];
    $exe = $pdo->prepare($query);
    $exe->execute($params_query);
    $row = $exe->fetchColumn();

    if ($row > 0) {
        $mensagem = "<p style='color:red;'>Não é possível excluir. Item está vinculado a pedido.</p>";
    } else {
        $query = "DELETE FROM item 
                        WHERE cod_item = :cod_item";

        $param_query = [':cod_item' => $cod_item];
        $exe = $pdo->prepare($query);
        $sucesso = $exe->execute($param_query); // executa o delete

        if ($sucesso) {
            $mensagem = "<p style='color:green;'>Item excluído com sucesso!</p>";
        } else {
            $mensagem = "<p style='color:red;'>Erro ao excluir item.</p>";
        }
    }
}
if (isset($_GET['editar'])) {
    $cod_item_editar = $_GET['editar'];

    $query = "SELECT *
                FROM item
               WHERE cod_item = :cod_item";

    $param_query = [':cod_item' => $cod_item_editar];
    $exe = $pdo->prepare($query);
    $exe->execute($param_query);
    $item = $exe->fetch(PDO::FETCH_ASSOC);

    if ($item) {
        $modo_edicao = true;
        $den_item_editar = $item['den_item'];
    }
}
if (isset($_POST['atualizar_item'])) {
    $cod_item = $_POST['cod_item'] ?? null;
    $den_item = trim($_POST['den_item'] ?? '');

    if ($cod_item && $den_item) {
        $query = "UPDATE item
                     SET den_item = :den_item
                   WHERE cod_item = :cod_item";

        $param_query = [':den_item' => $den_item,':cod_item' => $cod_item];           
        $exe = $pdo->prepare($query);
        $sucesso = $exe->execute($param_query);        
        

        if ($sucesso) {
            $mensagem = "<p style='color:green;'>Item atualizado com sucesso!</p>";
            // Limpa modo edição para voltar ao formulário de adicionar
            $modo_edicao = false;
            $cod_item_editar = "";
            $den_item_editar = "";
        } else {
            $mensagem = "<p style='color:red;'>Erro ao atualizar item.</p>";
        }
    } else {
        $mensagem = "<p style='color:red;'>Preencha o nome do item!</p>";
    }
}

if (isset($_POST['adicionar_item'])) {

    $den_item = trim($_POST['den_item'] ?? '');
    $cod_item = $_POST['cod_item'] ?? null;

    if ($den_item) {

        //  Pega o maior código existente
        $query = "SELECT MAX(cod_item) 
                    FROM item";

        $exe = $pdo->prepare($query);
        $exe->execute();
        $max = $exe->fetchColumn();
        $cod_item = $max ? $max + 1 : 1;

        //  Insere item
        $query = "INSERT INTO item (
                              cod_item, 
                              den_item) 
                       VALUES (
                              :cod_item, 
                              :den_item )";

        $param_query = [':cod_item' => $cod_item, ':den_item' => $den_item];
        $exe = $pdo->prepare($query);
        $sucesso = $exe->execute($param_query);
        
        if ($sucesso) {
            $mensagem = "<p style='color:green;'>Item adicionado com sucesso!</p>";
            $modo_edicao = false;
            $cod_item_editar = "";
            $den_item_editar = "";
        } else {
            $mensagem = "<p style='color:red;'>Erro ao adicionar item.</p>";
        }
    } else {
        $mensagem = "<p style='color:red;'>Preencha o nome do item!</p>";
    }
}
?>

<h2>Adicionar Item</h2>

<?= $mensagem ?>

<form method="post">
    <input type="hidden" name="cod_item" value="<?= $cod_item_editar ?>">

    Nome do Item:
    <input type="text" name="den_item" value="<?= htmlspecialchars($den_item_editar) ?>" required>
    <br><br>

    <button type="submit" name="<?= $modo_edicao ? 'atualizar_item' : 'adicionar_item' ?>">
        <?= $modo_edicao ? 'Atualizar' : 'Adicionar' ?>
    </button>
</form>

<h2>Lista de Itens</h2>

<table border="1" cellpadding="5">
    <tr>
        <th>Código</th>
        <th>Nome</th>
        <th>Açao</th>
    </tr>

    <?php
    $query = "SELECT * 
            FROM item 
        ORDER BY cod_item";

    $exe = $pdo->prepare($query);
    $exe->execute(); //se tirar a tabela nao aparece manter
    $itens = $exe->fetchAll(PDO::FETCH_ASSOC);

    foreach ($itens as $i) {
        echo "<tr>";

        echo "<td>" . htmlspecialchars($i['cod_item']) . "</td>";

        echo "<td>" . htmlspecialchars($i['den_item']) . "</td>";

        echo "<td>
            <a href='?editar=" . $i['cod_item'] . "'>Editar</a> |
            <a href='?excluir=" . $i['cod_item'] . "'
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