
<!DOCTYPE html>
<html>
<head>
  <title>Save Data</title>
</head>
<body>
  <button id="saveButton">Save</button>

  <div id="authModal" style="display:none;">
    <form id="authForm">
      <label for="authUsername">Username:</label>
      <input type="text" id="authUsername" name="authUsername" required>
      <label for="authPassword">Password:</label>
      <input type="password" id="authPassword" name="authPassword" required>
      <button type="submit">Authenticate</button>
    </form>
  </div>

  <script>
    document.getElementById('saveButton').addEventListener('click', function() {
      document.getElementById('authModal').style.display = 'block';
    });

    document.getElementById('authForm').addEventListener('submit', function(event) {
      event.preventDefault();
      const username = document.getElementById('authUsername').value;
      const password = document.getElementById('authPassword').value;
      // Verifique as credenciais aqui (exemplo simplificado)
      if (username === 'admin' && password === 'admin') {
        alert('Authentication successful');
        document.getElementById('authModal').style.display = 'none';
        // Execute a operação de salvar/atualizar aqui
      } else {
        alert('Invalid credentials');
      }
    });
  </script>
</body>
</html>
