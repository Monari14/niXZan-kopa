<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>kopa</title>
  <link rel="stylesheet" href="../../../css/style.css">
</head>
<body>
  <header>
    <nav class="navbar">
      <div class="navbar-logo">
        <a href="../index.php">
          <img src="../../../i/logo.png" alt="Logo"> kopa
        </a>
      </div>
      <div class="navbar-actions">
        <ul class="navbar-links">
          <li><a href="../index.php">Todas entregas</a></li>
          <li><a href="../entregas/index.php">Minhas entregas</a></li>
          <li><a href="pedidos/index.php">Meus pedidos</a></li>
          <button class="logout-btn" onclick="logout2()">Sair</button>
        </ul>
      </div>

      <div class="menu-toggle" id="menuToggle">
        <span></span>
        <span></span>
        <span></span>
      </div>
    </nav>
  </header>
  <main>
    <div class="container">
      <div class="esquerda">
        <?php include 'carrinho.php'; ?>
      </div>
      <div class="meio">
        <?php include 'produtos.php';?>
      </div>
      <div class="direita">
        <?php include 'pedidos.php'; ?>
      </div>
    </div>
  </main>
  <?php include '../footer.php';?>
  <script>
    function logout2() {
      localStorage.removeItem('token');
      localStorage.removeItem('user_id');
      localStorage.removeItem('user_name');
      localStorage.removeItem('role');
      window.location.href = '../../../index.php';
    }
    const menuToggle = document.getElementById("menuToggle");
    const navbarLinks = document.querySelector(".navbar-links");

    menuToggle.addEventListener("click", () => {
      navbarLinks.classList.toggle("active");
    });
  </script>
  <script src="../js/painel.js"></script>
</body>
</html>
