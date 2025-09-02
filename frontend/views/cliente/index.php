<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Painel de Produtos</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <header>
    <a class="navbar-brand" href="/">
      <img src="../../i/logo.png" alt="Logo"> kopa
    </a>
    <button onclick="logout()">Sair</button>
  </header>

  <main>
    <div class="container">
      <div class="esquerda">
        <ul id="carrinho"></ul>
        <div class="total">Total de produtos: <span id="total">0</span></div>
      </div>
      <div class="meio">
        <ul id="produtos"></ul>
      </div>
      <div class="direita">
        <ul id="pedidos"></ul>
      </div>
    </div>
  </main>
  <script src="js/painel.js"></script>
</body>
</html>
