<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
</head>
<body>
  <form id="loginForm">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required>
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>
    <button type="submit">Login</button>
  </form>

  <script>
    document.getElementById('loginForm').addEventListener('submit', function(event) {
      event.preventDefault();
      const username = document.getElementById('username').value;
      const password = document.getElementById('password').value;
      // Verifique as credenciais aqui (exemplo simplificado)
      if (username === 'admin' && password === 'admin') {
        alert('Login successful');
        // Redirecione para a página principal ou execute outras ações
      } else {
        alert('Invalid credentials');
      }
    });
  </script>
</body>
</html>
