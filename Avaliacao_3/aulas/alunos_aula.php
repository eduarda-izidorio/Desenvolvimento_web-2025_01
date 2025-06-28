<?php
session_start();
require_once '../includes/db_config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$message = '';
$id_aula = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$aula_info = null;
$alunos_nao_matriculados = [];
$alunos_matriculados = [];

if (!$id_aula) {
    $message = '<p class="error-message">ID da aula não fornecido.</p>';
} else {
    try {
        $stmt_aula = $pdo->prepare("SELECT a.data_aula, a.hora_inicio, a.hora_fim, m.nome_modalidade, p.nome_professor
                                    FROM aulas a
                                    JOIN modalidades m ON a.id_modalidade = m.id_modalidade
                                    JOIN professores p ON a.id_professor = p.id_professor
                                    WHERE a.id_aula = :id");
        $stmt_aula->bindParam(':id', $id_aula);
        $stmt_aula->execute();
        $aula_info = $stmt_aula->fetch(PDO::FETCH_ASSOC);

        if (!$aula_info) {
            $message = '<p class="error-message">Aula não encontrada.</p>';
            $id_aula = null;
        }

        if ($aula_info) {
            $stmt_matriculados = $pdo->prepare("SELECT al.id_aluno, al.nome_aluno, ma.data_matricula
                                                FROM alunos al
                                                JOIN matriculas_aula ma ON al.id_aluno = ma.id_aluno
                                                WHERE ma.id_aula = :id_aula ORDER BY al.nome_aluno");
            $stmt_matriculados->bindParam(':id_aula', $id_aula);
            $stmt_matriculados->execute();
            $alunos_matriculados = $stmt_matriculados->fetchAll(PDO::FETCH_ASSOC);

            $stmt_nao_matriculados = $pdo->prepare("SELECT id_aluno, nome_aluno FROM alunos
                                                    WHERE id_aluno NOT IN (SELECT id_aluno FROM matriculas_aula WHERE id_aula = :id_aula)
                                                    ORDER BY nome_aluno");
            $stmt_nao_matriculados->bindParam(':id_aula', $id_aula);
            $stmt_nao_matriculados->execute();
            $alunos_nao_matriculados = $stmt_nao_matriculados->fetchAll(PDO::FETCH_ASSOC);
        }

    } catch (PDOException $e) {
        $message = '<p class="error-message">Erro ao carregar dados dos alunos da aula: ' . $e->getMessage() . '</p>';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id_aula) {
    $action = $_POST['action'] ?? '';
    $id_aluno = filter_input(INPUT_POST, 'id_aluno', FILTER_SANITIZE_NUMBER_INT);

    if ($id_aluno) {
        try {
            if ($action === 'matricular') {
                $stmt = $pdo->prepare("INSERT INTO matriculas_aula (id_aluno, id_aula) VALUES (:id_aluno, :id_aula)");
                $stmt->bindParam(':id_aluno', $id_aluno);
                $stmt->bindParam(':id_aula', $id_aula);
                if ($stmt->execute()) {
                    $message = '<p class="success-message">Aluno matriculado com sucesso!</p>';
                } else {
                    $message = '<p class="error-message">Erro ao matricular aluno.</p>';
                }
            } elseif ($action === 'desmatricular') {
                $stmt = $pdo->prepare("DELETE FROM matriculas_aula WHERE id_aluno = :id_aluno AND id_aula = :id_aula");
                $stmt->bindParam(':id_aluno', $id_aluno);
                $stmt->bindParam(':id_aula', $id_aula);
                if ($stmt->execute()) {
                    $message = '<p class="success-message">Aluno desmatriculado com sucesso!</p>';
                } else {
                    $message = '<p class="error-message">Erro ao desmatricular aluno.</p>';
                }
            }
            header("Location: alunos_aula.php?id=" . $id_aula);
            exit();

        } catch (PDOException $e) {
            $message = '<p class="error-message">Erro no banco de dados: ' . $e->getMessage() . '</p>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alunos na Aula - Academia de Luta</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Alunos na Aula</h1>
        <?php if ($aula_info): ?>
            <h2><?php echo htmlspecialchars($aula_info['nome_modalidade']); ?> - <?php echo htmlspecialchars($aula_info['nome_professor']); ?></h2>
            <p>Data: <?php echo htmlspecialchars(date('d/m/Y', strtotime($aula_info['data_aula']))); ?></p>
            <p>Horário: <?php echo htmlspecialchars(date('H:i', strtotime($aula_info['hora_inicio'])) . ' - ' . date('H:i', strtotime($aula_info['hora_fim']))); ?></p>
        <?php endif; ?>

        <nav>
            <ul>
                <li><a href="../dashboard.php">Dashboard</a></li>
                <li><a href="index.php">Listar Aulas</a></li>
                <li><a href="../logout.php" style="color: #c00;">Sair</a></li>
            </ul>
        </nav>
        <br>
        <?php echo $message; ?>

        <h3>Alunos Matriculados (<?php echo count($alunos_matriculados); ?>)</h3>
        <?php if (empty($alunos_matriculados)): ?>
            <p>Nenhum aluno matriculado nesta aula ainda.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID Aluno</th>
                        <th>Nome do Aluno</th>
                        <th>Data Matrícula</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($alunos_matriculados as $aluno): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($aluno['id_aluno']); ?></td>
                        <td><?php echo htmlspecialchars($aluno['nome_aluno']); ?></td>
                        <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($aluno['data_matricula']))); ?></td>
                        <td>
                            <form action="alunos_aula.php?id=<?php echo $id_aula; ?>" method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="desmatricular">
                                <input type="hidden" name="id_aluno" value="<?php echo $aluno['id_aluno']; ?>">
                                <button type="submit" onclick="return confirm('Tem certeza que deseja desmatricular este aluno?');" style="background-color: #f44336;">Desmatricular</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <h3>Adicionar Aluno à Aula</h3>
        <?php if (empty($alunos_nao_matriculados)): ?>
            <p>Todos os alunos já estão matriculados nesta aula ou não há alunos cadastrados.</p>
        <?php else: ?>
            <form action="alunos_aula.php?id=<?php echo $id_aula; ?>" method="POST">
                <input type="hidden" name="action" value="matricular">
                <label for="aluno_para_matricular">Selecionar Aluno:</label>
                <select id="aluno_para_matricular" name="id_aluno" required>
                    <option value="">Selecione um aluno</option>
                    <?php foreach ($alunos_nao_matriculados as $aluno): ?>
                        <option value="<?php echo htmlspecialchars($aluno['id_aluno']); ?>">
                            <?php echo htmlspecialchars($aluno['nome_aluno']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Matricular Aluno</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>