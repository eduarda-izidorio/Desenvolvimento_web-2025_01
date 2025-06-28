<?php
session_start();
require_once '../includes/db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_type = $_SESSION['user_type'] ?? 'aluno';
$user_id = $_SESSION['user_id'];

$message = '';
$aulas = [];

if ($user_type === 'admin' && isset($_GET['action']) && $_GET['action'] === 'excluir' && isset($_GET['id'])) {
    $id_aula_excluir = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    if ($id_aula_excluir) {
        try {
            $pdo->beginTransaction();

            $stmt_delete_matriculas = $pdo->prepare("DELETE FROM matriculas_aula WHERE id_aula = :id");
            $stmt_delete_matriculas->bindParam(':id', $id_aula_excluir);
            $stmt_delete_matriculas->execute();

            $stmt_delete_aula = $pdo->prepare("DELETE FROM aulas WHERE id_aula = :id");
            $stmt_delete_aula->bindParam(':id', $id_aula_excluir);
            if ($stmt_delete_aula->execute()) {
                $pdo->commit();
                $message = '<p class="success-message">Aula e suas matrículas excluídas com sucesso!</p>';
            } else {
                $pdo->rollBack();
                $message = '<p class="error-message">Erro ao excluir aula.</p>';
            }
        } catch (PDOException $e) {
            $pdo->rollBack();
            $message = '<p class="error-message">Erro no banco de dados ao excluir aula: ' . $e->getMessage() . '</p>';
        }
    }
}

try {
    $sql = "SELECT a.id_aula, a.data_aula, a.hora_inicio, a.hora_fim, a.local_aula, a.capacidade_maxima,
                   m.nome_modalidade, p.nome_professor
            FROM aulas a
            JOIN modalidades m ON a.id_modalidade = m.id_modalidade
            JOIN professores p ON a.id_professor = p.id_professor";
    
    $params = [];

    if ($user_type === 'professor') {
        $stmt_prof_id = $pdo->prepare("SELECT id_professor FROM professores WHERE id_usuario = :user_id");
        $stmt_prof_id->bindParam(':user_id', $user_id);
        $stmt_prof_id->execute();
        $professor_db_id = $stmt_prof_id->fetchColumn();

        if ($professor_db_id) {
            $sql .= " WHERE a.id_professor = :professor_id";
            $params[':professor_id'] = $professor_db_id;
        } else {
            $message = '<p class="error-message">Não foi possível encontrar o ID do professor associado à sua conta.</p>';
        }
    } elseif ($user_type === 'aluno') {
        $stmt_aluno_id = $pdo->prepare("SELECT id_aluno FROM alunos WHERE id_usuario = :user_id");
        $stmt_aluno_id->bindParam(':user_id', $user_id);
        $stmt_aluno_id->execute();
        $aluno_db_id = $stmt_aluno_id->fetchColumn();

        if ($aluno_db_id) {
            $sql .= " JOIN matriculas_aula ma ON a.id_aula = ma.id_aula WHERE ma.id_aluno = :aluno_id";
            $params[':aluno_id'] = $aluno_db_id;
        } else {
            $message = '<p class="error-message">Não foi possível encontrar o ID do aluno associado à sua conta.</p>';
        }
    }

    $sql .= " ORDER BY a.data_aula DESC, a.hora_inicio ASC";
    
    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => &$val) {
        $stmt->bindParam($key, $val);
    }
    $stmt->execute();
    $aulas = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $message = '<p class="error-message">Erro ao carregar aulas: ' . $e->getMessage() . '</p>';
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Aulas - Academia de Luta</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .matricula-form {
            display: inline-block;
            margin: 0;
        }
        .matricula-form button {
            padding: 5px 10px;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>
            <?php 
                if ($user_type === 'admin') echo 'Gerenciar Aulas';
                elseif ($user_type === 'professor') echo 'Minhas Aulas';
                else echo 'Aulas Disponíveis / Minhas Matrículas';
            ?>
        </h1>
        <nav>
            <ul>
                <li><a href="../dashboard.php">Dashboard</a></li>
                <?php if ($user_type === 'admin'): ?>
                    <li><a href="adicionar.php">Adicionar Nova Aula</a></li>
                <?php endif; ?>
                <li><a href="../logout.php" style="color: #c00;">Sair</a></li>
            </ul>
        </nav>
        <br>
        <?php echo $message; ?>

        <?php if (empty($aulas)): ?>
            <p>Nenhuma aula encontrada para você. 
                <?php if ($user_type === 'admin'): ?>
                    <a href="adicionar.php">Adicione uma!</a>
                <?php endif; ?>
            </p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Modalidade</th>
                        <th>Professor</th>
                        <th>Data</th>
                        <th>Horário</th>
                        <th>Local</th>
                        <th>Vagas</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($aulas as $aula): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($aula['id_aula']); ?></td>
                        <td><?php echo htmlspecialchars($aula['nome_modalidade']); ?></td>
                        <td><?php echo htmlspecialchars($aula['nome_professor']); ?></td>
                        <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($aula['data_aula']))); ?></td>
                        <td><?php echo htmlspecialchars(date('H:i', strtotime($aula['hora_inicio'])) . ' - ' . date('H:i', strtotime($aula['hora_fim']))); ?></td>
                        <td><?php echo htmlspecialchars($aula['local_aula'] ?: 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($aula['capacidade_maxima'] ?: 'Ilimitada'); ?></td>
                        <td>
                            <?php if ($user_type === 'admin'): ?>
                                <a href="editar.php?id=<?php echo $aula['id_aula']; ?>">Editar</a> |
                                <a href="index.php?action=excluir&id=<?php echo $aula['id_aula']; ?>" onclick="return confirm('Tem certeza que deseja excluir esta aula e todas as suas matrículas?');" style="color: #c00;">Excluir</a>
                                <br>
                                <a href="alunos_aula.php?id=<?php echo $aula['id_aula']; ?>">Ver Alunos</a>
                            <?php elseif ($user_type === 'aluno'): 
                                $is_matriculado = false;
                                try {
                                    $stmt_check_matricula = $pdo->prepare("SELECT COUNT(*) FROM matriculas_aula WHERE id_aluno = :aluno_id AND id_aula = :aula_id");
                                    $stmt_check_matricula->bindParam(':aluno_id', $aluno_db_id);
                                    $stmt_check_matricula->bindParam(':aula_id', $aula['id_aula']);
                                    $stmt_check_matricula->execute();
                                    $is_matriculado = ($stmt_check_matricula->fetchColumn() > 0);
                                } catch (PDOException $e) {
                                }
                            ?>
                                <form class="matricula-form" action="processar_matricula.php" method="POST">
                                    <input type="hidden" name="id_aula" value="<?php echo $aula['id_aula']; ?>">
                                    <input type="hidden" name="id_aluno" value="<?php echo $aluno_db_id; ?>">
                                    <?php if ($is_matriculado): ?>
                                        <button type="submit" name="action" value="desmatricular" style="background-color: #f44336;">Desmatricular</button>
                                    <?php else: ?>
                                        <button type="submit" name="action" value="matricular" style="background-color: #4CAF50;">Matricular</button>
                                    <?php endif; ?>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>