<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <title>Energy Simple</title>

  <link rel="stylesheet" type="text/css" href="easyui/themes/default/easyui.css" />
  <link rel="stylesheet" type="text/css" href="easyui/themes/default/icon.css" />

  <script src="easyui/jquery.min.js"></script>
  <script src="easyui/jquery.easyui.min.js"></script>
  <script src="easyui/datagrid-detailview.js"></script>

  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      display: flex;
      min-height: 100vh;
      background: #f5f5f5;
    }

    /* Sidebar fixa */
    .sidebar {
      width: 220px;
      background-color: #fd6702ff; /* laranja */
      color: black;
      height: 100vh;
      position: fixed;
      top: 0;
      left: 0;
      padding: 20px;
      box-sizing: border-box;
      display: flex;
      flex-direction: column;
      gap: 10px;
      overflow-y: auto;
    }

    .sidebar h2 {
      margin-bottom: 20px;
      font-weight: bold;
      font-size: 20px;
      user-select: none;
    }

    .sidebar h3 {
      margin-bottom: 15px;
      user-select: none;
      font-size: 16px;
      font-weight: 600;
    }

    /* Menu itens como links */
    .menu-item {
      display: flex;
      align-items: center;
      padding: 10px 12px;
      cursor: pointer;
      border-radius: 6px;
      font-size: 16px;
      color: black;
      text-decoration: none;
      transition: background-color 0.3s, color 0.3s;
    }

    .menu-item .icon {
      margin-right: 10px;
      font-size: 18px;
    }

    .menu-item:hover {
      background-color: #333;
      color: white;
      text-decoration: none;
    }

    /* Conteúdo principal, respeitando a sidebar */
    .main-content {
      margin-left: 220px; /* Largura do menu */
      padding: 20px;
      flex-grow: 1;
      background: white;
      min-height: 100vh;
      box-sizing: border-box;
    }

    /* Ajustes tabela */
    #dg {
      width: 1000px !important;
      height: 500px !important;
    }
  </style>
</head>

<body>

  <nav class="sidebar">
    <h2>Energy ⚡ Simple</h2>
    <h3>Menu</h3>

    <!-- Use <a> para links, não div com href -->
    <a href="gerenciar_itens.php" class="menu-item"><span class="icon">⚡</span> Gerenciar Itens</a>
    <a href="gerencia_cliente.php" class="menu-item"><span class="icon">👤</span> Gerenciar Clientes</a>
    <a href="controle_pedidos.php" class="menu-item"><span class="icon">📋</span> Controle Pedidos</a>
    <a href="#" class="menu-item"><span class="icon">🔧</span> Configurações</a>
    <a href="#" class="menu-item"><span class="icon">❓</span> Ajuda</a>
    <a href="index.php" class="menu-item"><span class="icon">🚪</span> Sair</a>

    <h3>Contato</h3>
    <p>(47) 99100-2904</p>
  </nav>

  <main class="main-content">

    <table id="dg" title="Energy ⚡ Simple"
      url="/jeasyui/get_users.php"
      toolbar="#toolbar" pagination="true"
      fitColumns="true" singleSelect="true">
      <thead>
        <tr>
          <th field="firstname" width="50">Cliente</th>
          <th field="lastname" width="50">Pedido</th>
          <th field="phone" width="50">Sobre</th>
          <th field="email" width="50">Contato</th>
        </tr>
      </thead>
    </table>
    <div id="toolbar">
      <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="newItem()">Novo</a>
      <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="destroyItem()">Excluir</a>
      <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" plain="true">Editar</a>
    </div>

    <script>
      $(function () {
        $('#dg').datagrid({
          view: detailview,
          detailFormatter: function (index, row) {
            return '<div class="ddv"></div>';
          },
          onExpandRow: function (index, row) {
            var ddv = $(this).datagrid('getRowDetail', index).find('div.ddv');
            ddv.panel({
              border: false,
              cache: true,
              href: 'show_form.php?index=' + index,
              onLoad: function () {
                $('#dg').datagrid('fixDetailRowHeight', index);
                $('#dg').datagrid('selectRow', index);
                $('#dg').datagrid('getRowDetail', index).find('form').form('load', row);
              }
            });
            $('#dg').datagrid('fixDetailRowHeight', index);
          }
        });
      });

      function saveItem(index) {
        var row = $('#dg').datagrid('getRows')[index];
        var url = row.isNewRecord ? 'save_user.php' : 'update_user.php?id=' + row.id;
        $('#dg').datagrid('getRowDetail', index).find('form').form('submit', {
          url: url,
          onSubmit: function () {
            return $(this).form('validate');
          },
          success: function (data) {
            data = eval('(' + data + ')');
            data.isNewRecord = false;
            $('#dg').datagrid('collapseRow', index);
            $('#dg').datagrid('updateRow', {
              index: index,
              row: data
            });
          }
        });
      }

      function cancelItem(index) {
        var row = $('#dg').datagrid('getRows')[index];
        if (row.isNewRecord) {
          $('#dg').datagrid('deleteRow', index);
        } else {
          $('#dg').datagrid('collapseRow', index);
        }
      }

      function destroyItem() {
        var row = $('#dg').datagrid('getSelected');
        if (row) {
          $.messager.confirm('Confirm', 'Tem certeza que deseja remover este usuário?', function (r) {
            if (r) {
              var index = $('#dg').datagrid('getRowIndex', row);
              $.post('destroy_user.php', { id: row.id }, function () {
                $('#dg').datagrid('deleteRow', index);
              });
            }
          });
        }
      }

      function newItem() {
        $('#dg').datagrid('appendRow', { isNewRecord: true });
        var index = $('#dg').datagrid('getRows').length - 1;
        $('#dg').datagrid('expandRow', index);
        $('#dg').datagrid('selectRow', index);
      }
    </script>

  </main>

</body>

</html>