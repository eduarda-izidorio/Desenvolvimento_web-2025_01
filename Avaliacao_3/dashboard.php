<?php
session_start(); // Inicia a sessão PHP

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_name = $_SESSION['user_name'] ?? 'Usuário';
$user_type = $_SESSION['user_type'] ?? 'aluno';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Academia de Luta</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Bem-vindo(a) à Dashboard, <?php echo htmlspecialchars($user_name); ?>!</h1>
        <p>Você está logado como: **<?php echo htmlspecialchars(ucfirst($user_type)); ?>**</p>

        <nav>
            <ul>
                <?php if ($user_type === 'admin'): ?>
                    <li><a href="modalidades/index.php">Gerenciar Modalidades</a></li>
                    <li><a href="professores/index.php">Gerenciar Professores</a></li>
                    <li><a href="alunos/index.php">Gerenciar Alunos</a></li>
                    <li><a href="aulas/index.php">Gerenciar Aulas</a></li>
                <?php elseif ($user_type === 'professor'): ?>
                    <li><a href="aulas/index.php?professor=<?php echo $_SESSION['user_id']; ?>">Minhas Aulas</a></li>
                    <li><a href="modalidades/index.php">Ver Modalidades</a></li>
                    <?php elseif ($user_type === 'aluno'): ?>
                    <li><a href="aulas/index.php?aluno=<?php echo $_SESSION['user_id']; ?>">Minhas Matrículas</a></li>
                    <li><a href="modalidades/index.php">Ver Modalidades</a></li>
                    <?php endif; ?>
                <li><a href="logout.php" style="color: #c00;">Sair</a></li>
            </ul>
        </nav>

        <div style="margin-top: 30px; text-align: center;">
            <p>Este é o seu painel de controle. Use o menu acima para navegar pelas funcionalidades da academia.</p>
            </div>
    </div>
</body>
</html>