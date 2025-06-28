<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

require_once 'includes/db_config.php'; // Inclui o arquivo de configuração do banco de dados

$error_message = '';
$success_message = '';

$nome_completo = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_completo = htmlspecialchars(trim($_POST['nome_completo']));
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];

    if (empty($nome_completo) || empty($email) || empty($senha) || empty($confirmar_senha)) {
        $error_message = 'Por favor, preencha todos os campos.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Formato de e-mail inválido.';
    } elseif ($senha !== $confirmar_senha) {
        $error_message = 'A senha e a confirmação de senha não coincidem.';
    } elseif (strlen($senha) < 8) { // Exemplo de complexidade mínima: 8 caracteres
        $error_message = 'A senha deve ter no mínimo 8 caracteres.';
    } elseif (!preg_match('/[A-Z]/', $senha) || !preg_match('/[a-z]/', $senha) || !preg_match('/[0-9]/', $senha) || !preg_match('/[^A-Za-z0-9]/', $senha)) {
        $error_message = 'A senha deve conter pelo menos uma letra maiúscula, uma minúscula, um número e um caractere especial.';
    } else {
        try {
            $stmt_check = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE email = :email");
            $stmt_check->bindParam(':email', $email);
            $stmt_check->execute();

            if ($stmt_check->rowCount() > 0) {
                $error_message = 'Este e-mail já está cadastrado. Por favor, use outro e-mail ou faça login.';
            } else {
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

                $stmt_insert = $pdo->prepare("INSERT INTO usuarios (nome_completo, email, senha, tipo_usuario) VALUES (:nome_completo, :email, :senha, 'aluno')");

                $stmt_insert->bindParam(':nome_completo', $nome_completo);
                $stmt_insert->bindParam(':email', $email);
                $stmt_insert->bindParam(':senha', $senha_hash);

                if ($stmt_insert->execute()) {
                    $success_message = 'Cadastro realizado com sucesso! Você já pode <a href="login.php" style="color: #c00; text-decoration: none;">fazer login</a>.';
                    $nome_completo = '';
                    $email = '';
                } else {
                    $error_message = 'Erro ao cadastrar usuário. Tente novamente.';
                }
            }
        } catch (PDOException $e) {
            $error_message = "Erro no banco de dados: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Academia de Luta</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="container">
        <h1>Cadastro na Academia</h1>
        <?php if (!empty($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <?php if (!empty($success_message)): ?>
            <p class="success-message"><?php echo $success_message; ?></p>
        <?php endif; ?>

        <form action="cadastro.php" method="POST">
            <label for="nome_completo">Nome Completo:</label>
            <input type="text" id="nome_completo" name="nome_completo" required
                value="<?php echo htmlspecialchars($nome_completo); ?>">

            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($email); ?>">

            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required>

            <label for="confirmar_senha">Confirmar Senha:</label>
            <input type="password" id="confirmar_senha" name="confirmar_senha" required>

            <button type="submit">Cadastrar</button>
        </form>
        <p style="text-align: center; margin-top: 20px;">
            Já tem uma conta? <a href="login.php" style="color: #c00; text-decoration: none;">Faça login aqui</a>.
        </p>
    </div>
</body>

</html>