<?php
session_start();
require_once '../includes/db_config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$message = '';

if (isset($_GET['action']) && $_GET['action'] === 'excluir' && isset($_GET['id'])) {
    $id_professor_excluir = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    if ($id_professor_excluir) {
        try {
            $pdo->beginTransaction();

            $stmt_get_user_id = $pdo->prepare("SELECT id_usuario FROM professores WHERE id_professor = :id");
            $stmt_get_user_id->bindParam(':id', $id_professor_excluir);
            $stmt_get_user_id->execute();
            $user_id_associado = $stmt_get_user_id->fetchColumn();

            $stmt_delete_prof = $pdo->prepare("DELETE FROM professores WHERE id_professor = :id");
            $stmt_delete_prof->bindParam(':id', $id_professor_excluir);
            $stmt_delete_prof->execute();

            if ($user_id_associado) {
                $stmt_delete_user = $pdo->prepare("DELETE FROM usuarios WHERE id_usuario = :id_usuario");
                $stmt_delete_user->bindParam(':id_usuario', $user_id_associado);
                $stmt_delete_user->execute();
            }

            $pdo->commit(); 
            $message = '<p class="success-message">Professor e seu usuário associado (se houver) excluídos com sucesso!</p>';

        } catch (PDOException $e) {
            $pdo->rollBack(); 
            $message = '<p class="error-message">Erro no banco de dados ao excluir professor: ' . $e->getMessage() . '</p>';
        }
    }
}

try {
    $stmt = $pdo->query("SELECT p.id_professor, p.nome_professor, p.email AS email_professor, p.telefone, 
                                u.email AS email_usuario, u.tipo_usuario
                         FROM professores p
                         LEFT JOIN usuarios u ON p.id_usuario = u.id_usuario
                         ORDER BY p.nome_professor");
    $professores = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = '<p class="error-message">Erro ao carregar professores: ' . $e->getMessage() . '</p>';
    $professores = [];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Professores - Academia de Luta</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Gerenciar Professores</h1>
        <nav>
            <ul>
                <li><a href="../dashboard.php">Dashboard</a></li>
                <li><a href="../admin/cadastro_professor.php">Adicionar Novo Professor</a></li>
                <li><a href="../logout.php" style="color: #c00;">Sair</a></li>
            </ul>
        </nav>
        <br>
        <?php echo $message; ?>

        <?php if (empty($professores)): ?>
            <p>Nenhum professor cadastrado ainda. <a href="../admin/cadastro_professor.php">Adicione um!</a></p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>E-mail (Professor)</th>
                        <th>Telefone</th>
                        <th>E-mail (Login)</th>
                        <th>Tipo Usuário</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($professores as $professor): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($professor['id_professor']); ?></td>
                        <td><?php echo htmlspecialchars($professor['nome_professor']); ?></td>
                        <td><?php echo htmlspecialchars($professor['email_professor']); ?></td>
                        <td><?php echo htmlspecialchars($professor['telefone']); ?></td>
                        <td><?php echo htmlspecialchars($professor['email_usuario'] ?: 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars(ucfirst($professor['tipo_usuario'] ?: 'N/A')); ?></td>
                        <td>
                            <a href="editar.php?id=<?php echo $professor['id_professor']; ?>">Editar</a> |
                            <a href="index.php?action=excluir&id=<?php echo $professor['id_professor']; ?>" onclick="return confirm('Tem certeza que deseja excluir este professor e seu usuário associado?');" style="color: #c00;">Excluir</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>