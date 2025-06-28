<?php
session_start();
require_once '../includes/db_config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$message = '';
$id_aluno = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$aluno_data = null;

if ($id_aluno) {
    try {
        $stmt = $pdo->prepare("SELECT a.id_aluno, a.nome_aluno, a.email AS email_aluno, a.telefone, a.data_nascimento, a.id_usuario,
                                    u.email AS email_usuario
                             FROM alunos a
                             LEFT JOIN usuarios u ON a.id_usuario = u.id_usuario
                             WHERE a.id_aluno = :id");
        $stmt->bindParam(':id', $id_aluno);
        $stmt->execute();
        $aluno_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$aluno_data) {
            $message = '<p class="error-message">Aluno não encontrado.</p>';
            $id_aluno = null;
        }
    } catch (PDOException $e) {
        $message = '<p class="error-message">Erro ao carregar dados do aluno: ' . $e->getMessage() . '</p>';
        $id_aluno = null;
    }
} else {
    $message = '<p class="error-message">ID do aluno não fornecido.</p>';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id_aluno) {
    $nome_aluno = htmlspecialchars(trim($_POST['nome_aluno']));
    $email_aluno = filter_input(INPUT_POST, 'email_aluno', FILTER_SANITIZE_EMAIL);
    $telefone = htmlspecialchars(trim($_POST['telefone']));
    $data_nascimento = htmlspecialchars(trim($_POST['data_nascimento']));
    $email_usuario_login = filter_input(INPUT_POST, 'email_usuario_login', FILTER_SANITIZE_EMAIL);
    $id_usuario_associado = filter_input(INPUT_POST, 'id_usuario_associado', FILTER_SANITIZE_NUMBER_INT);
    $nova_senha = $_POST['nova_senha'];
    $confirmar_senha = $_POST['confirmar_senha'];

    if (empty($nome_aluno) || empty($email_aluno)) {
        $message = '<p class="error-message">Nome e e-mail do aluno são obrigatórios.</p>';
    } elseif (!filter_var($email_aluno, FILTER_VALIDATE_EMAIL)) {
        $message = '<p class="error-message">Formato de e-mail do aluno inválido.</p>';
    } elseif (!empty($email_usuario_login) && !filter_var($email_usuario_login, FILTER_VALIDATE_EMAIL)) {
        $message = '<p class="error-message">Formato de e-mail de login inválido.</p>';
    } elseif (!empty($email_usuario_login) && empty($nova_senha) && !$id_usuario_associado) { 
        $message = '<p class="error-message">Uma senha é obrigatória ao criar um novo usuário de login.</p>';
    } elseif (!empty($nova_senha) && $nova_senha !== $confirmar_senha) {
        $message = '<p class="error-message">A nova senha e a confirmação não coincidem.</p>';
    } elseif (!empty($nova_senha) && (strlen($nova_senha) < 8 || !preg_match('/[A-Z]/', $nova_senha) || !preg_match('/[a-z]/', $nova_senha) || !preg_match('/[0-9]/', $nova_senha) || !preg_match('/[^A-Za-z0-9]/', $nova_senha))) {
        $message = '<p class="error-message">A nova senha deve ter no mínimo 8 caracteres, uma letra maiúscula, uma minúscula, um número e um caractere especial.</p>';
    } else {
        try {
            $pdo->beginTransaction();

            $stmt_check_aluno_email = $pdo->prepare("SELECT COUNT(*) FROM alunos WHERE email = :email AND id_aluno != :id");
            $stmt_check_aluno_email->bindParam(':email', $email_aluno);
            $stmt_check_aluno_email->bindParam(':id', $id_aluno);
            $stmt_check_aluno_email->execute();
            if ($stmt_check_aluno_email->fetchColumn() > 0) {
                throw new Exception("Já existe outro aluno com este e-mail.");
            }

            $stmt_update_aluno = $pdo->prepare("UPDATE alunos SET nome_aluno = :nome, email = :email, telefone = :telefone, data_nascimento = :data_nascimento WHERE id_aluno = :id");
            $stmt_update_aluno->bindParam(':nome', $nome_aluno);
            $stmt_update_aluno->bindParam(':email', $email_aluno);
            $stmt_update_aluno->bindParam(':telefone', $telefone);
            $stmt_update_aluno->bindParam(':data_nascimento', $data_nascimento);
            $stmt_update_aluno->bindParam(':id', $id_aluno);
            $stmt_update_aluno->execute();

            if ($id_usuario_associado) {
                $stmt_check_user_email = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE email = :email AND id_usuario != :id_user");
                $stmt_check_user_email->bindParam(':email', $email_usuario_login);
                $stmt_check_user_email->bindParam(':id_user', $id_usuario_associado);
                $stmt_check_user_email->execute();
                if ($stmt_check_user_email->fetchColumn() > 0) {
                    throw new Exception("E-mail de login já em uso por outro usuário.");
                }

                $sql_update_user = "UPDATE usuarios SET nome_completo = :nome_usuario, email = :email_usuario";
                if (!empty($nova_senha)) {
                    $sql_update_user .= ", senha = :senha_hash";
                }
                $sql_update_user .= " WHERE id_usuario = :id_usuario";

                $stmt_update_user = $pdo->prepare($sql_update_user);
                $stmt_update_user->bindParam(':nome_usuario', $nome_aluno);
                $stmt_update_user->bindParam(':email_usuario', $email_usuario_login);
                if (!empty($nova_senha)) {
                    $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                    $stmt_update_user->bindParam(':senha_hash', $senha_hash);
                }
                $stmt_update_user->bindParam(':id_usuario', $id_usuario_associado);
                $stmt_update_user->execute();
                $aluno_data['email_usuario'] = $email_usuario_login;

            } elseif (!empty($email_usuario_login)) { 
                $stmt_check_new_user_email = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE email = :email");
                $stmt_check_new_user_email->bindParam(':email', $email_usuario_login);
                $stmt_check_new_user_email->execute();
                if ($stmt_check_new_user_email->fetchColumn() > 0) {
                    throw new Exception("E-mail de login já em uso por outro usuário.");
                }
                
                $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                $stmt_insert_user = $pdo->prepare("INSERT INTO usuarios (nome_completo, email, senha, tipo_usuario) VALUES (:nome, :email, :senha, 'aluno')");
                $stmt_insert_user->bindParam(':nome', $nome_aluno);
                $stmt_insert_user->bindParam(':email', $email_usuario_login);
                $stmt_insert_user->bindParam(':senha', $senha_hash);
                $stmt_insert_user->execute();
                $id_usuario_associado = $pdo->lastInsertId();

                $stmt_update_aluno_id_user = $pdo->prepare("UPDATE alunos SET id_usuario = :id_usuario WHERE id_aluno = :id_aluno");
                $stmt_update_aluno_id_user->bindParam(':id_usuario', $id_usuario_associado);
                $stmt_update_aluno_id_user->bindParam(':id_aluno', $id_aluno);
                $stmt_update_aluno_id_user->execute();
                $aluno_data['id_usuario'] = $id_usuario_associado; 
                $aluno_data['email_usuario'] = $email_usuario_login;
            } else if (empty($email_usuario_login) && $id_usuario_associado) { 
                $stmt_update_aluno_id_user = $pdo->prepare("UPDATE alunos SET id_usuario = NULL WHERE id_aluno = :id_aluno");
                $stmt_update_aluno_id_user->bindParam(':id_aluno', $id_aluno);
                $stmt_update_aluno_id_user->execute();

                $stmt_delete_user = $pdo->prepare("DELETE FROM usuarios WHERE id_usuario = :id_usuario");
                $stmt_delete_user->bindParam(':id_usuario', $id_usuario_associado);
                $stmt_delete_user->execute();
                $aluno_data['id_usuario'] = null;
                $aluno_data['email_usuario'] = null;
            }


            $pdo->commit();
            $message = '<p class="success-message">Aluno atualizado com sucesso!</p>';
            $aluno_data['nome_aluno'] = $nome_aluno;
            $aluno_data['email_aluno'] = $email_aluno;
            $aluno_data['telefone'] = $telefone;
            $aluno_data['data_nascimento'] = $data_nascimento;

        } catch (Exception $e) { 
            $pdo->rollBack();
            $message = '<p class="error-message">Erro ao atualizar aluno: ' . $e->getMessage() . '</p>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Aluno - Academia de Luta</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Editar Aluno</h1>
        <nav>
            <ul>
                <li><a href="../dashboard.php">Dashboard</a></li>
                <li><a href="index.php">Listar Alunos</a></li>
                <li><a href="../logout.php" style="color: #c00;">Sair</a></li>
            </ul>
        </nav>
        <br>
        <?php echo $message; ?>

        <?php if ($aluno_data): ?>
            <form action="editar.php?id=<?php echo htmlspecialchars($id_aluno); ?>" method="POST">
                <input type="hidden" name="id_aluno_associado" value="<?php echo htmlspecialchars($aluno_data['id_aluno']); ?>">
                <input type="hidden" name="id_usuario_associado" value="<?php echo htmlspecialchars($aluno_data['id_usuario']); ?>">

                <label for="nome_aluno">Nome Completo do Aluno:</label>
                <input type="text" id="nome_aluno" name="nome_aluno" required value="<?php echo htmlspecialchars($aluno_data['nome_aluno']); ?>">

                <label for="email_aluno">E-mail (Aluno - para contato):</label>
                <input type="email" id="email_aluno" name="email_aluno" required value="<?php echo htmlspecialchars($aluno_data['email_aluno']); ?>">
                
                <label for="telefone">Telefone:</label>
                <input type="text" id="telefone" name="telefone" value="<?php echo htmlspecialchars($aluno_data['telefone']); ?>">

                <label for="data_nascimento">Data de Nascimento:</label>
                <input type="date" id="data_nascimento" name="data_nascimento" value="<?php echo htmlspecialchars($aluno_data['data_nascimento']); ?>">
                
                <hr style="margin: 20px 0; border: 0; border-top: 1px solid #ccc;">

                <h2>Dados de Login (Opcional)</h2>
                <p>Preencha os campos abaixo se quiser que este aluno tenha acesso ao sistema com um login.</p>
                <p>Para remover o login, deixe o campo "E-mail para Login" vazio.</p>

                <label for="email_usuario_login">E-mail para Login:</label>
                <input type="email" id="email_usuario_login" name="email_usuario_login" value="<?php echo htmlspecialchars($aluno_data['email_usuario'] ?? ''); ?>" placeholder="Deixe vazio se o aluno não for logar">
                
                <label for="nova_senha">Nova Senha (apenas se for alterar ou criar login):</label>
                <input type="password" id="nova_senha" name="nova_senha" placeholder="Deixe vazio para manter a senha atual">

                <label for="confirmar_senha">Confirmar Nova Senha:</label>
                <input type="password" id="confirmar_senha" name="confirmar_senha" placeholder="Repita a nova senha">

                <button type="submit">Atualizar Aluno</button>
            </form>
        <?php else: ?>
            <p>Não foi possível carregar os dados do aluno para edição.</p>
        <?php endif; ?>
    </div>
</body>
</html>