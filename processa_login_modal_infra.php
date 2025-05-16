<?php
session_start();

// Dados de conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "escritorio_m2";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $usuario = $_POST["nome_usuario"];
        $senha = $_POST["senha"];
        $redirectUrl = $_POST['redirect_url'];
        $targetBlank = isset($_POST['target_blank']) && $_POST['target_blank'] === 'true';

        $stmt = $conn->prepare("SELECT id_usuarios, senha FROM usuarios WHERE nome_usuario = :usuario");
        $stmt->bindParam(':usuario', $usuario);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($resultado && password_verify($senha, $resultado["senha"])) {
            // Login bem-sucedido
            $_SESSION['usuario_id'] = $resultado['id_usuarios'];

            if ($targetBlank) {
                echo "<script>window.open('$redirectUrl', '_blank'); window.location.href = '/index.php';</script>";
            } else {
                echo "<script>window.location.href = '$redirectUrl';</script>";
            }
            exit();
        } else {
            // Falha no login
            header("Location: /index.php?login_erro_infra=1");
            exit();
        }
    }
} catch(PDOException $e) {
    echo "Erro na conexão com o banco de dados: " . $e->getMessage();
}
$conn = null;
?>