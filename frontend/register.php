<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro</title>
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
    <h2 style="text-align:center;">Registrar</h2>
    <form id="register">
        <label for="name">Nome completo:</label>
        <input type="text" id="name" placeholder="Seu nome completo">

        <label for="username">Username:</label>
        <input type="text" id="username" placeholder="Seu username">

        <label for="email">E-mail:</label>
        <input type="email" id="email" placeholder="Seu e-mail">

        <label for="telefone">Telefone:</label>
        <input type="text" id="telefone" placeholder="Seu telefone">

        <label for="password">Senha:</label>
        <input type="password" id="reg-password" placeholder="Sua senha">

        <label for="password_confirmation">Confirme sua senha:</label>
        <input type="password" id="password_confirmation" placeholder="Confirme sua senha">

        <button type="submit">Registrar</button>
        <ul class="navbar-links">
          <li><p>Já tem uma conta? <a href="index.php" style="color: #bb86fc;">Faça login</a></p></li>
        </ul>
    </form>
  <script src="js/theme.js"></script>
  <script src="js/auth/auth.js"></script>
  <script>
    const menuToggle = document.getElementById("menuToggle");
    const navbarLinks = document.querySelector(".navbar-links");

    menuToggle.addEventListener("click", () => {
      navbarLinks.classList.toggle("active");
    });
    
    document.getElementById('register').addEventListener('submit', function(e) {
      e.preventDefault();
      register();
    });
  </script>
</body>
</html>
