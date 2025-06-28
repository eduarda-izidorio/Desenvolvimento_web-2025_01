<?php
session_start();
require_once '../includes/db_config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$message = '';
$nome_modalidade = '';
$descricao = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_modalidade = htmlspecialchars(trim($_POST['nome_modalidade']));
    $descricao = htmlspecialchars(trim($_POST['descricao']));

    if (empty($nome_modalidade)) {
        $message = '<p class="error-message">O nome da modalidade é obrigatório.</p>';
    } else {
        try {
            $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM modalidades WHERE nome_modalidade = :nome");
            $stmt_check->bindParam(':nome', $nome_modalidade);
            $stmt_check->execute();
            if ($stmt_check->fetchColumn() > 0) {
                $message = '<p class="error-message">Já existe uma modalidade com este nome.</p>';
            } else {
                $stmt = $pdo->prepare("INSERT INTO modalidades (nome_modalidade, descricao) VALUES (:nome, :descricao)");
                $stmt->bindParam(':nome', $nome_modalidade);
                $stmt->bindParam(':descricao', $descricao);
                if ($stmt->execute()) {
                    $message = '<p class="success-message">Modalidade adicionada com sucesso!</p>';
                    $nome_modalidade = '';
                    $descricao = '';
                } else {
                    $message = '<p class="error-message">Erro ao adicionar modalidade.</p>';
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
    <title>Adicionar Modalidade - Academia de Luta</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Adicionar Nova Modalidade</h1>
        <nav>
            <ul>
                <li><a href="../dashboard.php">Dashboard</a></li>
                <li><a href="index.php">Listar Modalidades</a></li>
                <li><a href="../logout.php" style="color: #c00;">Sair</a></li>
            </ul>
        </nav>
        <br>
        <?php echo $message; ?>

        <form action="adicionar.php" method="POST">
            <label for="nome_modalidade">Nome da Modalidade:</label>
            <input type="text" id="nome_modalidade" name="nome_modalidade" required value="<?php echo htmlspecialchars($nome_modalidade); ?>">

            <label for="descricao">Descrição:</label>
            <textarea id="descricao" name="descricao" rows="4"><?php echo htmlspecialchars($descricao); ?></textarea>

            <button type="submit">Adicionar Modalidade</button>
        </form>
    </div>
</body>
</html>