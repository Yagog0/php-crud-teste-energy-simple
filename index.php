<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Energy Simple</title>

    <link rel="stylesheet" type="text/css" href="easyui/themes/default/easyui.css">
    <link rel="stylesheet" type="text/css" href="easyui/themes/icon.css">

    <script src="easyui/jquery.min.js"></script>
    <script src="easyui/jquery.easyui.min.js"></script>

    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: #070707ff;
        }

        .easyui-panel {
            box-shadow: 10 0 10px #fd6702ff;
            border-radius: 50px;
        }
    </style>
</head>

<body>

    <form method="post" action="gerenciar_pedidos.php">
        <div class="easyui-panel" style="width:400px;padding:50px 60px;">
            <h2 style=" text-align:center;">Energy Simple </h2>
            <!-- <h2 style="text-align:center;">Senha e Usuário</h2> -->
            <div style="margin-bottom:20px">
                <input class="easyui-textbox"
                    name="usuario"
                    prompt="Username"
                    iconWidth="28"
                    style="width:100%;height:34px;padding:10px;">
            </div>
            <div style="margin-bottom:20px">
                <input class="easyui-passwordbox"
                    name="senha"
                    prompt="Password"
                    iconWidth="28"
                    style="width:100%;height:34px;padding:10px;">
                <br><br><br>
                <button type="submit" class="btn-entrar">Entrar</button>

                <style>
                    .btn-entrar {
                        background-color: #FF6600;
                        /* laranja vibrante */
                        color: white;
                        font-weight: bold;
                        font-size: 18px;
                        padding: 12px 0;
                        width: 100%;
                        border: none;
                        border-radius: 8px;
                        cursor: pointer;
                        box-shadow: 0 5px 15px rgba(255, 102, 0, 0.6);
                        transition: background-color 0.3s ease, box-shadow 0.3s ease;
                    }

                    .btn-entrar:hover {
                        background-color: #e65500;
                        /* tom laranja mais escuro no hover */
                        box-shadow: 0 7px 20px rgba(230, 85, 0, 0.8);
                    }

                    .btn-entrar:active {
                        background-color: #cc4d00;
                        box-shadow: 0 3px 7px rgba(204, 77, 0, 0.6);
                        transform: translateY(2px);
                    }
                </style>

            </div>
    </form>

</body>

</html>


<!-- <p style=" text-align:center;"> 
                            <a href="gerenciar_pedidos.php">entra </a>
                        </p>
            </div>