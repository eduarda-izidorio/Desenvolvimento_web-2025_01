<?php
session_start();
require_once '../includes/db_config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$message = '';
$id_aula = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$aula_data = null;

try {
    $stmt_modalidades = $pdo->query("SELECT id_modalidade, nome_modalidade FROM modalidades ORDER BY nome_modalidade");
    $modalidades = $stmt_modalidades->fetchAll(PDO::FETCH_ASSOC);

    $stmt_professores = $pdo->query("SELECT id_professor, nome_professor FROM professores ORDER BY nome_professor");
    $professores = $stmt_professores->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = '<p class="error-message">Erro ao carregar dados para o formulário: ' . $e->getMessage() . '</p>';
    $modalidades = [];
    $professores = [];
}

if ($id_aula) {
    try {
        $stmt = $pdo->prepare("SELECT id_aula, id_modalidade, id_professor, data_aula, hora_inicio, hora_fim, local_aula, capacidade_maxima FROM aulas WHERE id_aula = :id");
        $stmt->bindParam(':id', $id_aula);
        $stmt->execute();
        $aula_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$aula_data) {
            $message = '<p class="error-message">Aula não encontrada.</p>';
            $id_aula = null;
        }
    } catch (PDOException $e) {
        $message = '<p class="error-message">Erro ao carregar dados da aula: ' . $e->getMessage() . '</p>';
        $id_aula = null;
    }
} else {
    $message = '<p class="error-message">ID da aula não fornecido.</p>';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id_aula) {
    $id_modalidade = filter_input(INPUT_POST, 'id_modalidade', FILTER_SANITIZE_NUMBER_INT);
    $id_professor = filter_input(INPUT_POST, 'id_professor', FILTER_SANITIZE_NUMBER_INT);
    $data_aula = htmlspecialchars(trim($_POST['data_aula']));
    $hora_inicio = htmlspecialchars(trim($_POST['hora_inicio']));
    $hora_fim = htmlspecialchars(trim($_POST['hora_fim']));
    $local_aula = htmlspecialchars(trim($_POST['local_aula']));
    $capacidade_maxima = filter_input(INPUT_POST, 'capacidade_maxima', FILTER_SANITIZE_NUMBER_INT);
    $id_para_atualizar = filter_input(INPUT_POST, 'id_aula', FILTER_SANITIZE_NUMBER_INT);

    if (empty($id_modalidade) || empty($id_professor) || empty($data_aula) || empty($hora_inicio) || empty($hora_fim)) {
        $message = '<p class="error-message">Por favor, preencha todos os campos obrigatórios.</p>';
    } elseif ($id_para_atualizar != $id_aula) {
         $message = '<p class="error-message">Erro de ID da aula. Tente novamente.</p>';
    } elseif (!in_array($id_modalidade, array_column($modalidades, 'id_modalidade'))) {
        $message = '<p class="error-message">Modalidade inválida selecionada.</p>';
    } elseif (!in_array($id_professor, array_column($professores, 'id_professor'))) {
        $message = '<p class="error-message">Professor inválido selecionado.</p>';
    } elseif ($hora_inicio >= $hora_fim) {
        $message = '<p class="error-message">A hora de início deve ser anterior à hora de fim.</p>';
    } elseif ($capacidade_maxima !== false && $capacidade_maxima < 1) {
        $message = '<p class="error-message">A capacidade máxima deve ser um número positivo ou vazio.</p>';
    }
    else {
        try {
            $stmt = $pdo->prepare("UPDATE aulas SET id_modalidade = :id_modalidade, id_professor = :id_professor, data_aula = :data_aula, hora_inicio = :hora_inicio, hora_fim = :hora_fim, local_aula = :local_aula, capacidade_maxima = :capacidade_maxima WHERE id_aula = :id");
            $stmt->bindParam(':id_modalidade', $id_modalidade);
            $stmt->bindParam(':id_professor', $id_professor);
            $stmt->bindParam(':data_aula', $data_aula);
            $stmt->bindParam(':hora_inicio', $hora_inicio);
            $stmt->bindParam(':hora_fim', $hora_fim);
            $stmt->bindParam(':local_aula', $local_aula);
            $capacidade_para_db = ($capacidade_maxima !== false && $capacidade_maxima !== 0) ? $capacidade_maxima : NULL;
            $stmt->bindParam(':capacidade_maxima', $capacidade_para_db, PDO::PARAM_INT);
            $stmt->bindParam(':id', $id_aula);
            
            if ($stmt->execute()) {
                $message = '<p class="success-message">Aula atualizada com sucesso!</p>';
                $aula_data['id_modalidade'] = $id_modalidade;
                $aula_data['id_professor'] = $id_professor;
                $aula_data['data_aula'] = $data_aula;
                $aula_data['hora_inicio'] = $hora_inicio;
                $aula_data['hora_fim'] = $hora_fim;
                $aula_data['local_aula'] = $local_aula;
                $aula_data['capacidade_maxima'] = $capacidade_maxima;
            } else {
                $message = '<p class="error-message">Erro ao atualizar aula.</p>';
            }
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
    <title>Editar Aula - Academia de Luta</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Editar Aula</h1>
        <nav>
            <ul>
                <li><a href="../dashboard.php">Dashboard</a></li>
                <li><a href="index.php">Listar Aulas</a></li>
                <li><a href="../logout.php" style="color: #c00;">Sair</a></li>
            </ul>
        </nav>
        <br>
        <?php echo $message; ?>

        <?php if ($aula_data): ?>
            <form action="editar.php?id=<?php echo htmlspecialchars($id_aula); ?>" method="POST">
                <input type="hidden" name="id_aula" value="<?php echo htmlspecialchars($aula_data['id_aula']); ?>">

                <label for="id_modalidade">Modalidade:</label>
                <select id="id_modalidade" name="id_modalidade" required>
                    <option value="">Selecione uma modalidade</option>
                    <?php foreach ($modalidades as $modalidade): ?>
                        <option value="<?php echo htmlspecialchars($modalidade['id_modalidade']); ?>"
                                <?php echo ($aula_data['id_modalidade'] == $modalidade['id_modalidade']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($modalidade['nome_modalidade']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="id_professor">Professor:</label>
                <select id="id_professor" name="id_professor" required>
                    <option value="">Selecione um professor</option>
                    <?php foreach ($professores as $professor): ?>
                        <option value="<?php echo htmlspecialchars($professor['id_professor']); ?>"
                                <?php echo ($aula_data['id_professor'] == $professor['id_professor']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($professor['nome_professor']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="data_aula">Data da Aula:</label>
                <input type="date" id="data_aula" name="data_aula" required value="<?php echo htmlspecialchars($aula_data['data_aula']); ?>">

                <label for="hora_inicio">Hora de Início:</label>
                <input type="time" id="hora_inicio" name="hora_inicio" required value="<?php echo htmlspecialchars($aula_data['hora_inicio']); ?>">

                <label for="hora_fim">Hora de Fim:</label>
                <input type="time" id="hora_fim" name="hora_fim" required value="<?php echo htmlspecialchars($aula_data['hora_fim']); ?>">

                <label for="local_aula">Local da Aula (opcional):</label>
                <input type="text" id="local_aula" name="local_aula" value="<?php echo htmlspecialchars($aula_data['local_aula']); ?>">

                <label for="capacidade_maxima">Capacidade Máxima de Alunos (opcional):</label>
                <input type="number" id="capacidade_maxima" name="capacidade_maxima" min="0" value="<?php echo htmlspecialchars($aula_data['capacidade_maxima']); ?>">

                <button type="submit">Atualizar Aula</button>
            </form>
        <?php else: ?>
            <p>Não foi possível carregar os dados da aula para edição.</p>
        <?php endif; ?>
    </div>
</body>
</html>