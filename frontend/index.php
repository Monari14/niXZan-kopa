<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login / Registro</title>
</head>
<body>

  <!-- Header -->
  <div>
    <h1>kopa</h1>
    <button id="theme-toggle">🌙</button>
  </div>
  <div>
    <!-- Cadastro -->
    <div>
      <h2>Registrar</h2>

      <input type="text" id="name" placeholder="Nome">

      <input type="text" id="username" placeholder="Username">

      <input type="email" id="email" placeholder="Email">

      <input type="text" id="telefone" placeholder="Telefone">

      <input type="password" id="reg-password" placeholder="Senha">

      <input type="password" id="password_confirmation" placeholder="Confirme a senha">

      <button onclick="register()">Registrar</button>
    </div>

    <!-- Login -->
    <div>
      <h2>Login</h2>

      <input type="text" id="login" placeholder="Email ou Username">

      <input type="password" id="password" placeholder="Senha">

      <button onclick="login()">Login</button>
    </div>
  </div>

  <script src="js/theme.js"></script>
  <script src="js/auth/auth.js"></script>
</body>
</html>
