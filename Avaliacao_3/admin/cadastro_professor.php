<?php
session_start();
require_once '../includes/db_config.php';

// Proteger esta página: Apenas administradores podem cadastrar professores
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php"); // Redireciona para login se não for admin
    exit();
}

$error_message = '';
$success_message = '';
$nome_completo = '';
$email = '';
$telefone = ''; // Novo campo para professor

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_completo = htmlspecialchars(trim($_POST['nome_completo']));
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    $telefone = htmlspecialchars(trim($_POST['telefone'])); // Pega o telefone

    // --- Validação dos Campos (Similar ao cadastro.php) ---
    if (empty($nome_completo) || empty($email) || empty($senha) || empty($confirmar_senha)) {
        $error_message = 'Por favor, preencha todos os campos obrigatórios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Formato de e-mail inválido.';
    } elseif ($senha !== $confirmar_senha) {
        $error_message = 'A senha e a confirmação de senha não coincidem.';
    } elseif (strlen($senha) < 8) {
        $error_message = 'A senha deve ter no mínimo 8 caracteres.';
    } elseif (!preg_match('/[A-Z]/', $senha) || !preg_match('/[a-z]/', $senha) || !preg_match('/[0-9]/', $senha) || !preg_match('/[^A-Za-z0-9]/', $senha)) {
        $error_message = 'A senha deve conter pelo menos uma letra maiúscula, uma minúscula, um número e um caractere especial.';
    } else {
        try {
            // Verificar se o e-mail já existe na tabela usuarios
            $stmt_check = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE email = :email");
            $stmt_check->bindParam(':email', $email);
            $stmt_check->execute();

            if ($stmt_check->rowCount() > 0) {
                $error_message = 'Este e-mail já está cadastrado para outro usuário.';
            } else {
                // Inserir na tabela usuarios primeiro (com tipo 'professor')
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                $pdo->beginTransaction(); // Inicia uma transação para garantir que ambas as inserções funcionem

                $stmt_usuario = $pdo->prepare("INSERT INTO usuarios (nome_completo, email, senha, tipo_usuario) VALUES (:nome_completo, :email, :senha, 'professor')");
                $stmt_usuario->bindParam(':nome_completo', $nome_completo);
                $stmt_usuario->bindParam(':email', $email);
                $stmt_usuario->bindParam(':senha', $senha_hash);
                $stmt_usuario->execute();

                $id_novo_usuario = $pdo->lastInsertId(); // Pega o ID do usuário recém-inserido

                // Inserir na tabela professores
                $stmt_professor = $pdo->prepare("INSERT INTO professores (nome_professor, email, telefone, id_usuario) VALUES (:nome_professor, :email_prof, :telefone, :id_usuario)");
                $stmt_professor->bindParam(':nome_professor', $nome_completo);
                $stmt_professor->bindParam(':email_prof', $email);
                $stmt_professor->bindParam(':telefone', $telefone);
                $stmt_professor->bindParam(':id_usuario', $id_novo_usuario);
                $stmt_professor->execute();

                $pdo->commit(); // Confirma a transação

                $success_message = 'Professor cadastrado com sucesso! ID do Usuário: ' . $id_novo_usuario;
                // Limpar campos
                $nome_completo = '';
                $email = '';
                $telefone = '';

            }
        } catch (PDOException $e) {
            $pdo->rollBack(); // Desfaz a transação em caso de erro
            $error_message = "Erro ao cadastrar professor: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Professor - Academia de Luta</title>
    <link rel="stylesheet" href="../css/style.css"> </head>
<body>
    <div class="container">
        <h1>Cadastrar Novo Professor</h1>
        <nav>
            <ul>
                <li><a href="../dashboard.php">Dashboard</a></li>
                <li><a href="index.php">Listar Professores</a></li>
                <li><a href="../logout.php" style="color: #c00;">Sair</a></li>
            </ul>
        </nav>
        <br>
        <?php if (!empty($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <?php if (!empty($success_message)): ?>
            <p class="success-message"><?php echo $success_message; ?></p>
        <?php endif; ?>

        <form action="cadastro_professor.php" method="POST">
            <label for="nome_completo">Nome Completo:</label>
            <input type="text" id="nome_completo" name="nome_completo" required value="<?php echo htmlspecialchars($nome_completo); ?>">

            <label for="email">E-mail (para Login):</label>
            <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($email); ?>">

            <label for="telefone">Telefone:</label>
            <input type="text" id="telefone" name="telefone" value="<?php echo htmlspecialchars($telefone); ?>">

            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required>

            <label for="confirmar_senha">Confirmar Senha:</label>
            <input type="password" id="confirmar_senha" name="confirmar_senha" required>

            <button type="submit">Cadastrar Professor</button>
        </form>
    </div>
</body>
</html>