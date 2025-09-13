<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Todos admins</title>
  <link rel="stylesheet" href="../../../css/style.css">
  <style>
    #editarSettingsModal {
      display: none;
      max-width: 500px;
      margin-top: 20px;
    }

    .modal-actions-editar {
      background: #23232a;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .modal-actions-editar button {
      padding: 0.8rem 1.5rem;
      border: none;
      border-radius: 4px;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    #salvarSettingsBtn {
      background-color: #28a745;
      color: #fff;
    }

    #salvarSettingsBtn:hover {
      background-color: #218838;
    }

    #fecharEditarSettingsBtn {
      background-color: #dc3545;
      color: #fff;
    }

    #fecharEditarSettingsBtn:hover {
      background-color: #c82333;
    }

    #editarSettingsForm {
      display: flex;
      flex-direction: column;
    }

    #editarSettingsForm label {
      text-align: left;
      font-weight: bold;
      margin-bottom: 0.5rem;
      color: #fff;
    }

    #editarSettingsForm input,
    #editarSettingsForm select {
      width: 100%;
      padding: 0.75rem;
      margin-bottom: 1.5rem;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 1rem;
      box-sizing: border-box;
      transition: border-color 0.3s ease;
    }

    #editarSettingsForm input:focus,
    #editarSettingsForm select:focus {
      outline: none;
      border-color: #007bff;
    }

    #editarSettingsForm button {
      font-size: 1rem;
    }
    #adicionarEntregadorModal {
      display: none;
    }

    .modal-actions-adicionar {
      background: #23232a;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .close {
      float: right;
      font-size: 22px;
      font-weight: bold;
      cursor: pointer;
    }

    .modal-actions-adicionar {
      margin-top: 15px;
      gap: 10px;
    }
    
    #adicionarForm {
      display: flex;
      flex-direction: column;
    }

    #adicionarForm label {
      text-align: left;
      font-weight: bold;
      margin-bottom: 0.5rem;
      color: #fff;
    }

    #adicionarForm input,
    #adicionarForm select {
      width: 100%;
      padding: 0.75rem;
      margin-bottom: 1.5rem;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 1rem;
      box-sizing: border-box;
      transition: border-color 0.3s ease;
    }

    #adicionarForm input:focus,
    #adicionarForm select:focus {
      outline: none;
      border-color: #007bff;
    }

    #adicionarForm button {
      padding: 0.8rem 1.5rem;
      border: none;
      border-radius: 4px;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    #adicionarForm button[type="submit"] {
      background-color: #28a745;
      color: #fff;
      margin-bottom: 0.75rem;
    }

    #adicionarForm button[type="submit"]:hover {
      background-color: #218838;
    }

    #cancelarAdicao {
      background-color: #dc3545;
      color: #fff;
    }

    #cancelarAdicao:hover {
      background-color: #c82333;
    }

    /* TABELA DE PEDIDOS */
    #todos_entregadores, #settings {
        background: var(--bg-card);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        border: 1px solid #222;
        overflow: hidden;
        margin-bottom: 2rem;
    }
    #todos_entregadores th, #settings th {
        background: var(--bg-header);
        color: var(--accent);
        font-weight: 700;
        padding: 0.9rem 0.5rem;
        font-size: 1rem;
        border-bottom: 2px solid var(--accent);
    }
    #todos_entregadores td, #settings td {
        padding: 0.7rem 0.5rem;
        color: var(--text-main);
        border-bottom: 1px solid #222;
        font-size: 1rem;
        vertical-align: top;
    }
    #todos_entregadores tr:nth-child(even) td, #settings tr:nth-child(even) td {
        background: var(--bg-hover);
    }
    #todos_entregadores tr:last-child td, #settings tr:last-child td {
        border-bottom: none;
    }
    #todos_entregadores tbody tr:hover td, #settings tbody tr:hover td {
        background: var(--bg-header);
        color: var(--accent);
        transition: background var(--transition), color var(--transition);
    }
    #todos_entregadores, #settings {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    /* Responsividade da tabela de pedidos */
    .tabela-responsive {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    @media (max-width: 900px) {
        #todos_entregadores, #settings {
            font-size: 0.95rem;
            min-width: 600px;
        }
    }

    @media (max-width: 600px) {
        .tabela-responsive {
            border-radius: 0;
            margin-left: -8px;
            margin-right: -8px;
        }
        #todos_entregadores, #settings {
            min-width: 480px;
            font-size: 0.88rem;
        }
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
                  <li><a href="../produtos/index.php">Produtos</a></li>
                  <li><a href="../clientes/index.php">Clientes</a></li>
                  <li><a href="../entregadores/index.php">Entregadores</a></li>
                  <li><a href="index.php">Adm</a></li>
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
        <?php include 'settings.php';?>
      </div>
      <div class="meio">
        <?php include 'todos_admins.php';?>
      </div>
      <div class="direita">
        <?php include 'adicionar_admin.php';?>
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
  <script src="../js/admins.js"></script>
  <script src="../js/painel.js"></script>
</body>
</html>
