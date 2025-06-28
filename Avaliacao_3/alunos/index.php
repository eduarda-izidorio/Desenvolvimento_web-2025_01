<?php
session_start();
require_once '../includes/db_config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$message = '';

if (isset($_GET['action']) && $_GET['action'] === 'excluir' && isset($_GET['id'])) {
    $id_aluno_excluir = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    if ($id_aluno_excluir) {
        try {
            $pdo->beginTransaction(); 

            $stmt_get_user_id = $pdo->prepare("SELECT id_usuario FROM alunos WHERE id_aluno = :id");
            $stmt_get_user_id->bindParam(':id', $id_aluno_excluir);
            $stmt_get_user_id->execute();
            $user_id_associado = $stmt_get_user_id->fetchColumn();

            $stmt_delete_matriculas = $pdo->prepare("DELETE FROM matriculas_aula WHERE id_aluno = :id");
            $stmt_delete_matriculas->bindParam(':id', $id_aluno_excluir);
            $stmt_delete_matriculas->execute();


            $stmt_delete_aluno = $pdo->prepare("DELETE FROM alunos WHERE id_aluno = :id");
            $stmt_delete_aluno->bindParam(':id', $id_aluno_excluir);
            $stmt_delete_aluno->execute();

            if ($user_id_associado) {
                $stmt_delete_user = $pdo->prepare("DELETE FROM usuarios WHERE id_usuario = :id_usuario");
                $stmt_delete_user->bindParam(':id_usuario', $user_id_associado);
                $stmt_delete_user->execute();
            }

            $pdo->commit(); 
            $message = '<p class="success-message">Aluno, suas matrículas e seu usuário associado (se houver) excluídos com sucesso!</p>';

        } catch (PDOException $e) {
            $pdo->rollBack();
            $message = '<p class="error-message">Erro no banco de dados ao excluir aluno: ' . $e->getMessage() . '</p>';
        }
    }
}

try {
    $stmt = $pdo->query("SELECT a.id_aluno, a.nome_aluno, a.email AS email_aluno, a.telefone, a.data_nascimento,
                                u.email AS email_usuario, u.tipo_usuario
                         FROM alunos a
                         LEFT JOIN usuarios u ON a.id_usuario = u.id_usuario
                         ORDER BY a.nome_aluno");
    $alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = '<p class="error-message">Erro ao carregar alunos: ' . $e->getMessage() . '</p>';
    $alunos = [];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Alunos - Academia de Luta</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Gerenciar Alunos</h1>
        <nav>
            <ul>
                <li><a href="../dashboard.php">Dashboard</a></li>
                <li><a href="adicionar.php">Adicionar Novo Aluno</a></li>
                <li><a href="../logout.php" style="color: #c00;">Sair</a></li>
            </ul>
        </nav>
        <br>
        <?php echo $message; ?>

        <?php if (empty($alunos)): ?>
            <p>Nenhum aluno cadastrado ainda. <a href="adicionar.php">Adicione um!</a></p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>E-mail (Aluno)</th>
                        <th>Telefone</th>
                        <th>Nascimento</th>
                        <th>E-mail (Login)</th>
                        <th>Tipo Usuário</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($alunos as $aluno): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($aluno['id_aluno']); ?></td>
                        <td><?php echo htmlspecialchars($aluno['nome_aluno']); ?></td>
                        <td><?php echo htmlspecialchars($aluno['email_aluno']); ?></td>
                        <td><?php echo htmlspecialchars($aluno['telefone']); ?></td>
                        <td><?php echo htmlspecialchars($aluno['data_nascimento'] ? date('d/m/Y', strtotime($aluno['data_nascimento'])) : 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($aluno['email_usuario'] ?: 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars(ucfirst($aluno['tipo_usuario'] ?: 'N/A')); ?></td>
                        <td>
                            <a href="editar.php?id=<?php echo $aluno['id_aluno']; ?>">Editar</a> |
                            <a href="index.php?action=excluir&id=<?php echo $aluno['id_aluno']; ?>" onclick="return confirm('Tem certeza que deseja excluir este aluno, suas matrículas e seu usuário associado?');" style="color: #c00;">Excluir</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>