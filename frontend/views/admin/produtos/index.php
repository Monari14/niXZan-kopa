<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Painel de Produtos</title>
  <link rel="stylesheet" href="../../../css/style.css">
  <style>
    #produtoModal1, #produtoModal, #adicionarProdutoModal {
      display: none;
    }

    .modal-content1, .modal-content, .modal-actions-adicionar {
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

    .modal-actions1, .modal-actions, .modal-actions-adicionar {
      margin-top: 15px;
      gap: 10px;
    }
    
    /* Estilos para o formulário */
    #editarForm, #adicionarForm {
      display: flex;
      flex-direction: column;
    }

    /* Estilos para os rótulos */
    #editarForm label, #adicionarForm label {
      text-align: left;
      font-weight: bold;
      margin-bottom: 0.5rem;
      color: #fff;
    }

    /* Estilos para os campos de entrada e seleção */
    #editarForm input, #adicionarForm input,
    #editarForm select, #adicionarForm select {
      width: 100%;
      padding: 0.75rem;
      margin-bottom: 1.5rem;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 1rem;
      box-sizing: border-box;
      transition: border-color 0.3s ease;
    }

    #editarForm input:focus, #adicionarForm input:focus,
    #editarForm select:focus, #adicionarForm select:focus {
      outline: none;
      border-color: #007bff; /* Cor de destaque ao focar */
    }

    /* Estilos para os botões */
    #editarForm button, #adicionarForm button {
      padding: 0.8rem 1.5rem;
      border: none;
      border-radius: 4px;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    /* Estilo para o botão de salvar */
    #editarForm button[type="submit"], #adicionarForm button[type="submit"] {
      background-color: #28a745; /* Verde */
      color: #fff;
      margin-bottom: 0.75rem;
    }

    #editarForm button[type="submit"]:hover, #adicionarForm button[type="submit"]:hover {
      background-color: #218838; /* Verde mais escuro */
    }

    /* Estilo para o botão de cancelar */
    #cancelarEdicao, #cancelarAdicao {
      background-color: #dc3545; /* Vermelho */
      color: #fff;
    }

    #cancelarEdicao:hover, #cancelarAdicao:hover {
      background-color: #c82333; /* Vermelho mais escuro */
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
                  <li><a href="../clientes/index.php">Clientes</a></li>
                  <li><a href="../entregadores/index.php">Entregadores</a></li>
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
        <?php include 'modal_produtos.php';?>
      </div>
      <div class="meio">
        <?php include 'todos_produtos.php';?>
      </div>
      <div class="direita">
        <?php include 'adicionar_produtos.php';?>
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
  <script src="../js/painel.js"></script>
</body>
</html>
