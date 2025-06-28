<?php
session_start();
require_once '../includes/db_config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$message = '';

if (isset($_GET['action']) && $_GET['action'] === 'excluir' && isset($_GET['id'])) {
    $id_modalidade = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    if ($id_modalidade) {
        try {
            $stmt = $pdo->prepare("DELETE FROM modalidades WHERE id_modalidade = :id");
            $stmt->bindParam(':id', $id_modalidade);
            if ($stmt->execute()) {
                $message = '<p class="success-message">Modalidade excluída com sucesso!</p>';
            } else {
                $message = '<p class="error-message">Erro ao excluir modalidade.</p>';
            }
        } catch (PDOException $e) {
            $message = '<p class="error-message">Erro no banco de dados ao excluir: ' . $e->getMessage() . '</p>';
        }
    }
}

try {
    $stmt = $pdo->query("SELECT id_modalidade, nome_modalidade, descricao FROM modalidades ORDER BY nome_modalidade");
    $modalidades = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = '<p class="error-message">Erro ao carregar modalidades: ' . $e->getMessage() . '</p>';
    $modalidades = [];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Modalidades - Academia de Luta</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Gerenciar Modalidades</h1>
        <nav>
            <ul>
                <li><a href="../dashboard.php">Dashboard</a></li>
                <li><a href="adicionar.php">Adicionar Nova Modalidade</a></li>
                <li><a href="../logout.php" style="color: #c00;">Sair</a></li>
            </ul>
        </nav>
        <br>
        <?php echo $message; ?>

        <?php if (empty($modalidades)): ?>
            <p>Nenhuma modalidade cadastrada ainda. <a href="adicionar.php">Adicione uma!</a></p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($modalidades as $modalidade): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($modalidade['id_modalidade']); ?></td>
                        <td><?php echo htmlspecialchars($modalidade['nome_modalidade']); ?></td>
                        <td><?php echo htmlspecialchars($modalidade['descricao']); ?></td>
                        <td>
                            <a href="editar.php?id=<?php echo $modalidade['id_modalidade']; ?>">Editar</a> |
                            <a href="index.php?action=excluir&id=<?php echo $modalidade['id_modalidade']; ?>" onclick="return confirm('Tem certeza que deseja excluir esta modalidade?');" style="color: #c00;">Excluir</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>