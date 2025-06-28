<?php
session_start();
require_once '../includes/db_config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$message = '';
$id_modalidade = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$modalidade_data = null;

if ($id_modalidade) {
    try {
        $stmt = $pdo->prepare("SELECT id_modalidade, nome_modalidade, descricao FROM modalidades WHERE id_modalidade = :id");
        $stmt->bindParam(':id', $id_modalidade);
        $stmt->execute();
        $modalidade_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$modalidade_data) {
            $message = '<p class="error-message">Modalidade não encontrada.</p>';
            $id_modalidade = null;
        }
    } catch (PDOException $e) {
        $message = '<p class="error-message">Erro ao carregar dados da modalidade: ' . $e->getMessage() . '</p>';
        $id_modalidade = null;
    }
} else {
    $message = '<p class="error-message">ID da modalidade não fornecido.</p>';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id_modalidade) {
    $nome_modalidade = htmlspecialchars(trim($_POST['nome_modalidade']));
    $descricao = htmlspecialchars(trim($_POST['descricao']));
    $id_para_atualizar = filter_input(INPUT_POST, 'id_modalidade', FILTER_SANITIZE_NUMBER_INT);

    if (empty($nome_modalidade)) {
        $message = '<p class="error-message">O nome da modalidade é obrigatório.</p>';
    } elseif ($id_para_atualizar != $id_modalidade) {
         $message = '<p class="error-message">Erro de ID de modalidade. Tente novamente.</p>';
    } else {
        try {
            $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM modalidades WHERE nome_modalidade = :nome AND id_modalidade != :id");
            $stmt_check->bindParam(':nome', $nome_modalidade);
            $stmt_check->bindParam(':id', $id_modalidade);
            $stmt_check->execute();
            if ($stmt_check->fetchColumn() > 0) {
                $message = '<p class="error-message">Já existe outra modalidade com este nome.</p>';
            } else {
                $stmt = $pdo->prepare("UPDATE modalidades SET nome_modalidade = :nome, descricao = :descricao WHERE id_modalidade = :id");
                $stmt->bindParam(':nome', $nome_modalidade);
                $stmt->bindParam(':descricao', $descricao);
                $stmt->bindParam(':id', $id_modalidade);
                
                if ($stmt->execute()) {
                    $message = '<p class="success-message">Modalidade atualizada com sucesso!</p>';
                    $modalidade_data['nome_modalidade'] = $nome_modalidade;
                    $modalidade_data['descricao'] = $descricao;
                } else {
                    $message = '<p class="error-message">Erro ao atualizar modalidade.</p>';
                }
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
    <title>Editar Modalidade - Academia de Luta</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Editar Modalidade</h1>
        <nav>
            <ul>
                <li><a href="../dashboard.php">Dashboard</a></li>
                <li><a href="index.php">Listar Modalidades</a></li>
                <li><a href="../logout.php" style="color: #c00;">Sair</a></li>
            </ul>
        </nav>
        <br>
        <?php echo $message; ?>

        <?php if ($modalidade_data): ?>
            <form action="editar.php?id=<?php echo htmlspecialchars($id_modalidade); ?>" method="POST">
                <input type="hidden" name="id_modalidade" value="<?php echo htmlspecialchars($modalidade_data['id_modalidade']); ?>">

                <label for="nome_modalidade">Nome da Modalidade:</label>
                <input type="text" id="nome_modalidade" name="nome_modalidade" required value="<?php echo htmlspecialchars($modalidade_data['nome_modalidade']); ?>">

                <label for="descricao">Descrição:</label>
                <textarea id="descricao" name="descricao" rows="4"><?php echo htmlspecialchars($modalidade_data['descricao']); ?></textarea>

                <button type="submit">Atualizar Modalidade</button>
            </form>
        <?php else: ?>
            <p>Não foi possível carregar os dados da modalidade para edição.</p>
        <?php endif; ?>
    </div>
</body>
</html>