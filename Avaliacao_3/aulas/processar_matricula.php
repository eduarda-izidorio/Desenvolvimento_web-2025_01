<?php
session_start();
require_once '../includes/db_config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'aluno') {
    header("Location: ../login.php");
    exit();
}

$message = '';
$id_aluno_logado = $_SESSION['user_id'];

try {
    $stmt_aluno_id = $pdo->prepare("SELECT id_aluno FROM alunos WHERE id_usuario = :user_id");
    $stmt_aluno_id->bindParam(':user_id', $id_aluno_logado);
    $stmt_aluno_id->execute();
    $aluno_db_id = $stmt_aluno_id->fetchColumn();

    if (!$aluno_db_id) {
        $_SESSION['message_error'] = 'Sua conta de usuário não está vinculada a um registro de aluno.';
        header("Location: index.php");
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['message_error'] = 'Erro ao verificar seu registro de aluno: ' . $e->getMessage();
    header("Location: index.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id_aula = filter_input(INPUT_POST, 'id_aula', FILTER_SANITIZE_NUMBER_INT);

    if ($id_aula && ($action === 'matricular' || $action === 'desmatricular')) {
        try {
            if ($action === 'matricular') {
                $stmt_capacidade = $pdo->prepare("SELECT capacidade_maxima FROM aulas WHERE id_aula = :id_aula");
                $stmt_capacidade->bindParam(':id_aula', $id_aula);
                $stmt_capacidade->execute();
                $capacidade_maxima = $stmt_capacidade->fetchColumn();

                $stmt_vagas_ocupadas = $pdo->prepare("SELECT COUNT(*) FROM matriculas_aula WHERE id_aula = :id_aula");
                $stmt_vagas_ocupadas->bindParam(':id_aula', $id_aula);
                $stmt_vagas_ocupadas->execute();
                $vagas_ocupadas = $stmt_vagas_ocupadas->fetchColumn();

                if ($capacidade_maxima !== null && $vagas_ocupadas >= $capacidade_maxima) {
                    $_SESSION['message_error'] = 'Não há vagas disponíveis nesta aula.';
                } else {
                    $stmt = $pdo->prepare("INSERT INTO matriculas_aula (id_aluno, id_aula) VALUES (:id_aluno, :id_aula)");
                    $stmt->bindParam(':id_aluno', $aluno_db_id);
                    $stmt->bindParam(':id_aula', $id_aula);
                    if ($stmt->execute()) {
                        $_SESSION['message_success'] = 'Matrícula realizada com sucesso!';
                    } else {
                        $_SESSION['message_error'] = 'Erro ao matricular-se. Você já pode estar matriculado ou houve um erro.';
                    }
                }
            } elseif ($action === 'desmatricular') {
                $stmt = $pdo->prepare("DELETE FROM matriculas_aula WHERE id_aluno = :id_aluno AND id_aula = :id_aula");
                $stmt->bindParam(':id_aluno', $aluno_db_id);
                $stmt->bindParam(':id_aula', $id_aula);
                if ($stmt->execute()) {
                    $_SESSION['message_success'] = 'Desmatrícula realizada com sucesso!';
                } else {
                    $_SESSION['message_error'] = 'Erro ao desmatricular-se.';
                }
            }
        } catch (PDOException $e) {
            $_SESSION['message_error'] = 'Erro no banco de dados: ' . $e->getMessage();
        }
    } else {
        $_SESSION['message_error'] = 'Ação inválida ou aula não especificada.';
    }
}
header("Location: index.php");
exit();
?>