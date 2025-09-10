<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Meus pedidos</title>
  <link rel="stylesheet" href="../../../css/style.css">
  <style>
    /* TABELA DE PEDIDOS */
    #meus_pedidos {
        background: var(--bg-card);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        border: 1px solid #222;
        overflow: hidden;
        margin-bottom: 2rem;
    }
    #meus_pedidos th {
        background: var(--bg-header);
        color: var(--accent);
        font-weight: 700;
        padding: 0.9rem 0.5rem;
        font-size: 1rem;
        border-bottom: 2px solid var(--accent);
    }
    #meus_pedidos td {
        padding: 0.7rem 0.5rem;
        color: var(--text-main);
        border-bottom: 1px solid #222;
        font-size: 1rem;
        vertical-align: top;
    }
    #meus_pedidos tr:nth-child(even) td {
        background: var(--bg-hover);
    }
    #meus_pedidos tr:last-child td {
        border-bottom: none;
    }
    #meus_pedidos tbody tr:hover td {
        background: var(--bg-header);
        color: var(--accent);
        transition: background var(--transition), color var(--transition);
    }
    #meus_pedidos {
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
        #meus_pedidos {
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
        #meus_pedidos {
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
                  <li><a href="index.php">Meus pedidos</a></li>
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
        <?php include 'meus_pedidos.php';?>
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
