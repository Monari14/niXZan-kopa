<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Painel de Produtos</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    button { background: #bb86fc; color: #121212; border: none; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer; font-size: 0.9rem; transition: 0.2s; }
    button:hover { background: #9b4dfc; }
  </style>
</head>
<body>
  <header>
    <a class="navbar-brand" href="#">
        <img src="../../i/logo.png" alt="Logo"> kopa
    </a>
    <button onclick="logout()">Sair</button>
  </header>
  <main>
    <div class="container">
      <div class="esquerda">
        <?php include 'painel/carrinho.php'; ?>
      </div>
      <div class="meio">
        <?php include 'painel/produtos.php';?>
      </div>
      <div class="direita">
        <?php include 'painel/pedidos.php'; ?>
      </div>
    </div>
  </main>
  <?php include 'footer.php';?>
  <script src="js/painel.js"></script>
</body>
</html>
