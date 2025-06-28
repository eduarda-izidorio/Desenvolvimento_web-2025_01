<?php
session_start();
require_once '../includes/db_config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$message = '';
$id_professor = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$professor_data = null;

if ($id_professor) {
    try {
        $stmt = $pdo->prepare("SELECT p.id_professor, p.nome_professor, p.email AS email_professor, p.telefone, p.id_usuario,
                                    u.email AS email_usuario
                             FROM professores p
                             LEFT JOIN usuarios u ON p.id_usuario = u.id_usuario
                             WHERE p.id_professor = :id");
        $stmt->bindParam(':id', $id_professor);
        $stmt->execute();
        $professor_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$professor_data) {
            $message = '<p class="error-message">Professor não encontrado.</p>';
            $id_professor = null;
        }
    } catch (PDOException $e) {
        $message = '<p class="error-message">Erro ao carregar dados do professor: ' . $e->getMessage() . '</p>';
        $id_professor = null;
    }
} else {
    $message = '<p class="error-message">ID do professor não fornecido.</p>';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id_professor) {
    $nome_professor = htmlspecialchars(trim($_POST['nome_professor']));
    $email_professor = filter_input(INPUT_POST, 'email_professor', FILTER_SANITIZE_EMAIL);
    $telefone = htmlspecialchars(trim($_POST['telefone']));
    $email_usuario_login = filter_input(INPUT_POST, 'email_usuario_login', FILTER_SANITIZE_EMAIL); 
    $id_usuario_associado = filter_input(INPUT_POST, 'id_usuario_associado', FILTER_SANITIZE_NUMBER_INT);
    $nova_senha = $_POST['nova_senha'];
    $confirmar_senha = $_POST['confirmar_senha'];

    if (empty($nome_professor) || empty($email_professor)) {
        $message = '<p class="error-message">Nome e e-mail do professor são obrigatórios.</p>';
    } elseif (!filter_var($email_professor, FILTER_VALIDATE_EMAIL)) {
        $message = '<p class="error-message">Formato de e-mail do professor inválido.</p>';
    } elseif (!empty($email_usuario_login) && !filter_var($email_usuario_login, FILTER_VALIDATE_EMAIL)) {
        $message = '<p class="error-message">Formato de e-mail de login inválido.</p>';
    } elseif (!empty($nova_senha) && $nova_senha !== $confirmar_senha) {
        $message = '<p class="error-message">A nova senha e a confirmação não coincidem.</p>';
    } elseif (!empty($nova_senha) && (strlen($nova_senha) < 8 || !preg_match('/[A-Z]/', $nova_senha) || !preg_match('/[a-z]/', $nova_senha) || !preg_match('/[0-9]/', $nova_senha) || !preg_match('/[^A-Za-z0-9]/', $nova_senha))) {
        $message = '<p class="error-message">A nova senha deve ter no mínimo 8 caracteres, uma letra maiúscula, uma minúscula, um número e um caractere especial.</p>';
    } else {
        try {
            $pdo->beginTransaction();

            $stmt_check_prof_email = $pdo->prepare("SELECT COUNT(*) FROM professores WHERE email = :email AND id_professor != :id");
            $stmt_check_prof_email->bindParam(':email', $email_professor);
            $stmt_check_prof_email->bindParam(':id', $id_professor);
            $stmt_check_prof_email->execute();
            if ($stmt_check_prof_email->fetchColumn() > 0) {
                $message = '<p class="error-message">Já existe outro professor com este e-mail.</p>';
            } else {
                $stmt_update_prof = $pdo->prepare("UPDATE professores SET nome_professor = :nome, email = :email, telefone = :telefone WHERE id_professor = :id");
                $stmt_update_prof->bindParam(':nome', $nome_professor);
                $stmt_update_prof->bindParam(':email', $email_professor);
                $stmt_update_prof->bindParam(':telefone', $telefone);
                $stmt_update_prof->bindParam(':id', $id_professor);
                $stmt_update_prof->execute();

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
                    $stmt_update_user->bindParam(':nome_usuario', $nome_professor);
                    $stmt_update_user->bindParam(':email_usuario', $email_usuario_login);
                    if (!empty($nova_senha)) {
                        $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                        $stmt_update_user->bindParam(':senha_hash', $senha_hash);
                    }
                    $stmt_update_user->bindParam(':id_usuario', $id_usuario_associado);
                    $stmt_update_user->execute();
                    $professor_data['email_usuario'] = $email_usuario_login;
                } elseif (!empty($email_usuario_login)) { 
                    $stmt_check_new_user_email = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE email = :email");
                    $stmt_check_new_user_email->bindParam(':email', $email_usuario_login);
                    $stmt_check_new_user_email->execute();
                    if ($stmt_check_new_user_email->fetchColumn() > 0) {
                        throw new Exception("E-mail de login já em uso por outro usuário.");
                    }

                    if (empty($nova_senha)) {
                        throw new Exception("Uma senha é obrigatória ao criar um novo usuário de login.");
                    }
                    
                    $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                    $stmt_insert_user = $pdo->prepare("INSERT INTO usuarios (nome_completo, email, senha, tipo_usuario) VALUES (:nome, :email, :senha, 'professor')");
                    $stmt_insert_user->bindParam(':nome', $nome_professor);
                    $stmt_insert_user->bindParam(':email', $email_usuario_login);
                    $stmt_insert_user->bindParam(':senha', $senha_hash);
                    $stmt_insert_user->execute();
                    $id_usuario_associado = $pdo->lastInsertId();

                    // Atualizar o professor para associar o novo id_usuario
                    $stmt_update_prof_id_user = $pdo->prepare("UPDATE professores SET id_usuario = :id_usuario WHERE id_professor = :id_prof");
                    $stmt_update_prof_id_user->bindParam(':id_usuario', $id_usuario_associado);
                    $stmt_update_prof_id_user->bindParam(':id_prof', $id_professor);
                    $stmt_update_prof_id_user->execute();
                    $professor_data['id_usuario'] = $id_usuario_associado;
                    $professor_data['email_usuario'] = $email_usuario_login;
                }

                $pdo->commit();
                $message = '<p class="success-message">Professor atualizado com sucesso!</p>';
                $professor_data['nome_professor'] = $nome_professor;
                $professor_data['email_professor'] = $email_professor;
                $professor_data['telefone'] = $telefone;

            }
        } catch (Exception $e) {
            $pdo->rollBack();
            $message = '<p class="error-message">Erro ao atualizar professor: ' . $e->getMessage() . '</p>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Professor - Academia de Luta</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Editar Professor</h1>
        <nav>
            <ul>
                <li><a href="../dashboard.php">Dashboard</a></li>
                <li><a href="index.php">Listar Professores</a></li>
                <li><a href="../logout.php" style="color: #c00;">Sair</a></li>
            </ul>
        </nav>
        <br>
        <?php echo $message; ?>

        <?php if ($professor_data): ?>
            <form action="editar.php?id=<?php echo htmlspecialchars($id_professor); ?>" method="POST">
                <input type="hidden" name="id_professor_associado" value="<?php echo htmlspecialchars($professor_data['id_professor']); ?>">
                <input type="hidden" name="id_usuario_associado" value="<?php echo htmlspecialchars($professor_data['id_usuario']); ?>">

                <label for="nome_professor">Nome Completo do Professor:</label>
                <input type="text" id="nome_professor" name="nome_professor" required value="<?php echo htmlspecialchars($professor_data['nome_professor']); ?>">

                <label for="email_professor">E-mail (Professor):</label>
                <input type="email" id="email_professor" name="email_professor" required value="<?php echo htmlspecialchars($professor_data['email_professor']); ?>">
                
                <label for="telefone">Telefone:</label>
                <input type="text" id="telefone" name="telefone" value="<?php echo htmlspecialchars($professor_data['telefone']); ?>">
                
                <hr style="margin: 20px 0; border: 0; border-top: 1px solid #ccc;">

                <h2>Dados de Login (Opcional)</h2>
                <p>Preencha os campos abaixo se quiser vincular um login ou alterar os dados de login existentes para este professor.</p>

                <label for="email_usuario_login">E-mail para Login:</label>
                <input type="email" id="email_usuario_login" name="email_usuario_login" value="<?php echo htmlspecialchars($professor_data['email_usuario'] ?? ''); ?>" placeholder="Deixe vazio se não houver login">
                
                <label for="nova_senha">Nova Senha (apenas se for alterar ou criar login):</label>
                <input type="password" id="nova_senha" name="nova_senha" placeholder="Deixe vazio para manter a senha atual">

                <label for="confirmar_senha">Confirmar Nova Senha:</label>
                <input type="password" id="confirmar_senha" name="confirmar_senha" placeholder="Repita a nova senha">

                <button type="submit">Atualizar Professor</button>
            </form>
        <?php else: ?>
            <p>Não foi possível carregar os dados do professor para edição.</p>
        <?php endif; ?>
    </div>
</body>
</html>