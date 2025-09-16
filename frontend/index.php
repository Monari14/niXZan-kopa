<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <header>
      <nav class="navbar">
        <div class="navbar-logo">
          <a href="index.php">
              <img src="i/logo.png" alt="Logo"> kopa
          </a>
        </div>
      <div class="navbar-actions">
        <ul class="navbar-links">
          <li><a href="index.php">Login</a></li>
          <li><a href="register.php">Registre-se</a></li>
        </ul>
      </div>

      <div class="menu-toggle" id="menuToggle">
        <span></span>
        <span></span>
        <span></span>
      </div>
    </nav>
  </header>
  <div>
  <br>
  <h2 style="text-align:center;">Login</h2>
  <form id="login">
    <div id="login-error" style="color: #cf6679; margin-top: 10px;"></div>
      <label for="email_username">E-mail ou username:</label>
      <input type="text" id="email_username" placeholder="Seu e-mail ou username">

      <label for="login_password">Senha:</label>
      <input type="password" id="login_password" placeholder="Sua senha">
      
      <button type="submit">Login</button>
      <ul class="navbar-links">
        <li><p>Não tem uma conta? <a href="register.php" style="color: #bb86fc;">Registre-se</a></p></li>
      </ul>
  </form>

  <?php include 'footer.php';?>
  <script src="js/theme.js"></script>
  <script src="js/auth/auth.js"></script>
  <script>
    const menuToggle = document.getElementById("menuToggle");
    const navbarLinks = document.querySelector(".navbar-links");

    menuToggle.addEventListener("click", () => {
      navbarLinks.classList.toggle("active");
    });

    document.getElementById('login').addEventListener('submit', function(e) {
      e.preventDefault();
      login();
    });
  </script>
</body>
</html>
