<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Resumo do pedido</title>
  <link rel="stylesheet" href="../../../css/style.css">
  <style>
    button { background: #bb86fc; color: #121212; border: none; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer; font-size: 0.9rem; transition: 0.2s; }
    button:hover { background: #9b4dfc; }
  </style>
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
                    <li><a href="pedidos/index.php">Pedidos</a></li>
                    <li><a href="produtos/index.php">Produtos</a></li>
                    <li><a href="clientes/index.php">Clientes</a></li>
                    <li><a href="entregadores/index.php">Entregadores</a></li>
                    <button class="logout-btn" onclick="logout()">Sair</button>
                </ul>
            </div>

            <div class="menu-toggle" id="menuToggle">
            <span></span>
            <span></span>
            <span></span>
            </div>
        </nav>
    </header>
    <main style="text-align: center;">
        <div class="container">
            <div class="meio">
                <?php include 'resumo_pedido.php'; ?>
            </div>
        </div>
    </main>
    <?php include '../footer.php';?>
    <script>
        const menuToggle = document.getElementById("menuToggle");
        const navbarLinks = document.querySelector(".navbar-links");

        menuToggle.addEventListener("click", () => {
        navbarLinks.classList.toggle("active");
        });
    </script>
    <script src="../js/resumo.js"></script>
</body>
</html>
