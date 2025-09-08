<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Painel de Produtos</title>
  <link rel="stylesheet" href="../../../css/style.css">
  <style>
    #produtoModal1, #produtoModal {
      display: none;
    }

    .modal-content1, .modal-content {
      background: #23232a;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .close1, .close {
      float: right;
      font-size: 22px;
      font-weight: bold;
      cursor: pointer;
    }

    .modal-actions1. .modal-actions {
      margin-top: 15px;
      gap: 10px;
    }
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
                  <li><a href="../pedidos/index.php">Pedidos</a></li>
                  <li><a href="index.php">Produtos</a></li>
                  <li><a href="/clientes.php">Clientes</a></li>
                  <li><a href="/entregadores.php">Entregadores</a></li>
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

  <main>
    <div class="container">
      <div class="esquerda">
        <h2>Produto</h2>
        <center>
          <div id="produtoModal1" class="modal1">
            <div class="modal-content1">
              <span id="closeModal1" class="close1">&times;</span>
              <h3 id="modalNome1"></h3>
              <img id="modalImagem1" src="" alt="" style="max-width: 200px; display: block; margin-bottom: 10px;">
              <p><strong>Tipo:</strong> <span id="modalTipo1"></span></p>
              <p><strong>Preço:</strong> R$ <span id="modalPreco1"></span></p>
              <p><strong>Estoque:</strong> <span id="modalEstoque1"></span></p>
                <div class="modal-actions1">
                    <button id="editarBtn1">Editar</button>
                    <button id="deletarBtn1">Deletar</button>
                </div>
            </div>
          </div>
            <div id="produtoModal" class="modal">
            <div class="modal-content">
              <span id="closeModal" class="close">&times;</span>
              <h3 id="modalNome"></h3>
              <img id="modalImagem" src="" alt="" style="max-width: 200px; display: block; margin-bottom: 10px;">
              <p><strong>Tipo:</strong> <span id="modalTipo"></span></p>
              <p><strong>Preço:</strong> R$ <span id="modalPreco"></span></p>
              <p><strong>Estoque:</strong> <span id="modalEstoque"></span></p>
                <div class="modal-actions">
                    <button id="editarBtn">Editar</button>
                    <button id="deletarBtn">Deletar</button>
                </div>
            </div>
          </div>
        </center>
      </div>
      <div class="meio">
        <h2 style="text-align:center;">Todos produtos</h2>
        <ul id="produtos"></ul>
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
  <script src="../js/produtos.js"></script>
</body>
</html>
