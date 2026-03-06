<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <a href="gerenciar_pedidos.php"> Vamos começa</a>
    <br><br>
    <a href="nadaaqui.php">almenta o som da caixa de som</a>
</body>
</html> -->

<!<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
 <a href="gerenciar_pedidos.php"> Clica em mim confia</a>
<style>
body {
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: #f0f0f0;
}

button {
  position: absolute;
  padding: 15px 30px;
  font-size: 18px;
  border-radius: 8px;
  border: none;
  background-color: #ff5722;
  color: white;
  cursor: pointer;
  transition: 0.2s;
}
</style>
</head>
<body>

<button id="foge" onclick="window.location.href='nadaaqui.php';">!___  🤠  ___!</button>

<script>
const btn = document.getElementById('foge');

document.addEventListener('mousemove', (e) => {
  const mouseX = e.clientX;
  const mouseY = e.clientY;
  
  const btnRect = btn.getBoundingClientRect();
  const btnX = btnRect.left + btnRect.width / 2;
  const btnY = btnRect.top + btnRect.height / 2;

  const distance = Math.hypot(mouseX - btnX, mouseY - btnY);

  if (distance < 100) { // distância mínima para fugir
    const offsetX = (btnX - mouseX) / distance * 100;
    const offsetY = (btnY - mouseY) / distance * 100;
    btn.style.left = `${btn.offsetLeft + offsetX}px`;
    btn.style.top = `${btn.offsetTop + offsetY}px`;
  }
});
</script>

</body>
</html>



