<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

require_once 'includes/db_config.php'; 
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha']; 

    if (empty($email) || empty($senha)) {
        $error_message = 'Por favor, preencha todos os campos.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Formato de e-mail inválido.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id_usuario, nome_completo, senha, tipo_usuario FROM usuarios WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($senha, $user['senha'])) {
                $_SESSION['user_id'] = $user['id_usuario'];
                $_SESSION['user_name'] = $user['nome_completo'];
                $_SESSION['user_type'] = $user['tipo_usuario'];

                header("Location: dashboard.php");
                exit();
            } else {
                $error_message = 'E-mail ou senha incorretos.';
            }
        } catch (PDOException $e) {
            $error_message = "Erro ao tentar fazer login: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Academia de Luta</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Login na Academia</h1>
        <?php if (!empty($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($email ?? ''); ?>">

            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required>

            <button type="submit">Entrar</button>
        </form>
        <p style="text-align: center; margin-top: 20px;">
            Ainda não tem uma conta? <a href="cadastro.php" style="color: #c00; text-decoration: none;">Cadastre-se aqui</a>.
        </p>
    </div>
</body>
</html>