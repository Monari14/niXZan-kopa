<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Painel de Produtos</title>
  <link rel="stylesheet" href="../../../css/style.css">
  <style>
    /* TABELA DE PEDIDOS */
    #todos_pedidos {
        background: var(--bg-card);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        border: 1px solid #222;
        overflow: hidden;
        margin-bottom: 2rem;
    }
    #todos_pedidos th {
        background: var(--bg-header);
        color: var(--accent);
        font-weight: 700;
        padding: 0.9rem 0.5rem;
        font-size: 1rem;
        border-bottom: 2px solid var(--accent);
    }
    #todos_pedidos td {
        padding: 0.7rem 0.5rem;
        color: var(--text-main);
        border-bottom: 1px solid #222;
        font-size: 1rem;
        vertical-align: top;
    }
    #todos_pedidos tr:nth-child(even) td {
        background: var(--bg-hover);
    }
    #todos_pedidos tr:last-child td {
        border-bottom: none;
    }
    #todos_pedidos tbody tr:hover td {
        background: var(--bg-header);
        color: var(--accent);
        transition: background var(--transition), color var(--transition);
    }
    #todos_pedidos {
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
        #todos_pedidos {
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
        #todos_pedidos {
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
                  <li><a href="#">Pedidos</a></li>
                  <li><a href="/produtos.php">Produtos</a></li>
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
      <div class="meio">
        <?php include 'todos_pedidos.php';?>
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
  <script src="../js/pedidos.js"></script>
  <script src="../js/painel.js"></script>
</body>
</html>
