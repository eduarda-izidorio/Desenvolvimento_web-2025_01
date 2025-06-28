<?php
session_start();
require_once '../includes/db_config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$message = '';
$nome_aluno = '';
$email_aluno = '';
$telefone = '';
$data_nascimento = '';
$email_usuario_login = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_aluno = htmlspecialchars(trim($_POST['nome_aluno']));
    $email_aluno = filter_input(INPUT_POST, 'email_aluno', FILTER_SANITIZE_EMAIL);
    $telefone = htmlspecialchars(trim($_POST['telefone']));
    $data_nascimento = htmlspecialchars(trim($_POST['data_nascimento']));
    $email_usuario_login = filter_input(INPUT_POST, 'email_usuario_login', FILTER_SANITIZE_EMAIL);
    $senha_login = $_POST['senha_login'];
    $confirmar_senha_login = $_POST['confirmar_senha_login'];

    if (empty($nome_aluno) || empty($email_aluno)) {
        $message = '<p class="error-message">Nome e e-mail do aluno são obrigatórios.</p>';
    } elseif (!filter_var($email_aluno, FILTER_VALIDATE_EMAIL)) {
        $message = '<p class="error-message">Formato de e-mail do aluno inválido.</p>';
    } elseif (!empty($email_usuario_login) && !filter_var($email_usuario_login, FILTER_VALIDATE_EMAIL)) {
        $message = '<p class="error-message">Formato de e-mail para login inválido.</p>';
    } elseif (!empty($email_usuario_login) && empty($senha_login)) {
        $message = '<p class="error-message">Uma senha é obrigatória se um e-mail para login for fornecido.</p>';
    } elseif (!empty($senha_login) && $senha_login !== $confirmar_senha_login) {
        $message = '<p class="error-message">A senha de login e a confirmação não coincidem.</p>';
    } elseif (!empty($senha_login) && (strlen($senha_login) < 8 || !preg_match('/[A-Z]/', $senha_login) || !preg_match('/[a-z]/', $senha_login) || !preg_match('/[0-9]/', $senha_login) || !preg_match('/[^A-Za-z0-9]/', $senha_login))) {
        $message = '<p class="error-message">A senha de login deve ter no mínimo 8 caracteres, uma letra maiúscula, uma minúscula, um número e um caractere especial.</p>';
    } else {
        try {
            $pdo->beginTransaction();

            $stmt_check_aluno_email = $pdo->prepare("SELECT COUNT(*) FROM alunos WHERE email = :email");
            $stmt_check_aluno_email->bindParam(':email', $email_aluno);
            $stmt_check_aluno_email->execute();
            if ($stmt_check_aluno_email->fetchColumn() > 0) {
                throw new Exception("Já existe um aluno com este e-mail.");
            }

            $id_usuario_associado = null;
            if (!empty($email_usuario_login)) {
                $stmt_check_user_email = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE email = :email");
                $stmt_check_user_email->bindParam(':email', $email_usuario_login);
                $stmt_check_user_email->execute();
                if ($stmt_check_user_email->fetchColumn() > 0) {
                    throw new Exception("O e-mail para login já está em uso por outro usuário.");
                }

                $senha_hash = password_hash($senha_login, PASSWORD_DEFAULT);
                $stmt_usuario = $pdo->prepare("INSERT INTO usuarios (nome_completo, email, senha, tipo_usuario) VALUES (:nome_completo, :email, :senha, 'aluno')");
                $stmt_usuario->bindParam(':nome_completo', $nome_aluno);
                $stmt_usuario->bindParam(':email', $email_usuario_login);
                $stmt_usuario->bindParam(':senha', $senha_hash);
                $stmt_usuario->execute();
                $id_usuario_associado = $pdo->lastInsertId();
            }

            $stmt_aluno = $pdo->prepare("INSERT INTO alunos (nome_aluno, email, telefone, data_nascimento, id_usuario) VALUES (:nome, :email, :telefone, :data_nascimento, :id_usuario)");
            $stmt_aluno->bindParam(':nome', $nome_aluno);
            $stmt_aluno->bindParam(':email', $email_aluno);
            $stmt_aluno->bindParam(':telefone', $telefone);
            $stmt_aluno->bindParam(':data_nascimento', $data_nascimento);
            $stmt_aluno->bindParam(':id_usuario', $id_usuario_associado, PDO::PARAM_INT);
            $stmt_aluno->execute();

            $pdo->commit();
            $message = '<p class="success-message">Aluno cadastrado com sucesso!</p>';
            $nome_aluno = '';
            $email_aluno = '';
            $telefone = '';
            $data_nascimento = '';
            $email_usuario_login = '';

        } catch (Exception $e) {
            $pdo->rollBack();
            $message = '<p class="error-message">Erro ao cadastrar aluno: ' . $e->getMessage() . '</p>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Aluno - Academia de Luta</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Adicionar Novo Aluno</h1>
        <nav>
            <ul>
                <li><a href="../dashboard.php">Dashboard</a></li>
                <li><a href="index.php">Listar Alunos</a></li>
                <li><a href="../logout.php" style="color: #c00;">Sair</a></li>
            </ul>
        </nav>
        <br>
        <?php echo $message; ?>

        <form action="adicionar.php" method="POST">
            <label for="nome_aluno">Nome Completo do Aluno:</label>
            <input type="text" id="nome_aluno" name="nome_aluno" required value="<?php echo htmlspecialchars($nome_aluno); ?>">

            <label for="email_aluno">E-mail (Aluno - para contato):</label>
            <input type="email" id="email_aluno" name="email_aluno" required value="<?php echo htmlspecialchars($email_aluno); ?>">
            
            <label for="telefone">Telefone:</label>
            <input type="text" id="telefone" name="telefone" value="<?php echo htmlspecialchars($telefone); ?>">

            <label for="data_nascimento">Data de Nascimento:</label>
            <input type="date" id="data_nascimento" name="data_nascimento" value="<?php echo htmlspecialchars($data_nascimento); ?>">
            
            <hr style="margin: 20px 0; border: 0; border-top: 1px solid #ccc;">

            <h2>Dados de Login (Opcional)</h2>
            <p>Preencha os campos abaixo se quiser que este aluno tenha acesso ao sistema com um login.</p>

            <label for="email_usuario_login">E-mail para Login:</label>
            <input type="email" id="email_usuario_login" name="email_usuario_login" value="<?php echo htmlspecialchars($email_usuario_login); ?>" placeholder="Deixe vazio se o aluno não for logar">
            
            <label for="senha_login">Senha (apenas se o e-mail para login for preenchido):</label>
            <input type="password" id="senha_login" name="senha_login" placeholder="Senha para o login do aluno">

            <label for="confirmar_senha_login">Confirmar Senha:</label>
            <input type="password" id="confirmar_senha_login" name="confirmar_senha_login" placeholder="Repita a senha para o login do aluno">

            <button type="submit">Cadastrar Aluno</button>
        </form>
    </div>
</body>
</html>