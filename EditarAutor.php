<?php
session_start();
require_once 'conexao.php';

// Proteção da página
if (!isset($_SESSION['usuario_id'])) {
    header('Location: Login.php');
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Se não tiver ID válido na URL, volta para a lista
if (!$id) {
    header('Location: Autores.php');
    exit;
}

$mensagem = '';
$tipoMensagem = '';

// Processa a atualização
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome          = trim($_POST['nome'] ?? '');
    $nascimento    = !empty($_POST['nascimento']) ? $_POST['nascimento'] : null;
    $nacionalidade = trim($_POST['nacionalidade'] ?? '');

    if (empty($nome)) {
        $mensagem = 'O nome do autor não pode ficar vazio.';
        $tipoMensagem = 'aviso';
    } else {
        try {
            $sqlUpdate = "UPDATE autores SET nome = ?, nascimento = ?, nacionalidade = ? WHERE id = ?";
            $stmtUpdate = $pdo->prepare($sqlUpdate);
            $stmtUpdate->execute([$nome, $nascimento, $nacionalidade, $id]);

            $mensagem = 'Autor atualizado com sucesso! <a href="Autores.php">Voltar para a lista</a>';
            $tipoMensagem = 'sucesso';
        } catch (PDOException $e) {
            $mensagem = 'Erro ao atualizar autor: ' . $e->getMessage();
            $tipoMensagem = 'erro';
        }
    }
}

// Busca os dados atuais do autor para preencher o formulário
try {
    $stmt = $pdo->prepare("SELECT * FROM autores WHERE id = ?");
    $stmt->execute([$id]);
    $autor = $stmt->fetch();

    if (!$autor) {
        header('Location: Autores.php');
        exit;
    }
} catch (PDOException $e) {
    die("Erro ao carregar dados do autor: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Autor - Livraria</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .alerta {
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 4px;
            text-align: center;
        }
        .alerta.sucesso { background-color: #d1e7dd; color: #0f5132; }
        .alerta.erro { background-color: #f8d7da; color: #842029; }
        .alerta.aviso { background-color: #fff3cd; color: #664d03; }
        .link-cancelar {
            display: inline-block;
            margin-top: 10px;
            color: #666;
            text-decoration: none;
        }
        .link-cancelar:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <nav class="navbar">
        <div class="logo">
            Livraria
        </div>

        <div class="menu">
            <a href="Principal.php">Início</a>

            <div class="dropdown">
                <a href="#">Livros ▾</a>
                <div class="dropdown-menu">
                    <a href="Livro.php">Ver livros</a>
                    <a href="CadastroLivro.php">Cadastrar livro</a>
                </div>
            </div>

            <div class="dropdown">
                <a href="#">Autores ▾</a>
                <div class="dropdown-menu">
                    <a href="Autores.php">Ver autores</a>
                    <a href="CadastroAutores.php">Cadastrar autor</a>
                </div>
            </div>

            <a href="logout.php">Deslogar</a>
        </div>
    </nav>

    <main class="pagina">
        <div class="formulario">
            <h1>Editar autor</h1>

            <?php if (!empty($mensagem)): ?>
                <div class="alerta <?= $tipoMensagem ?>">
                    <?= $mensagem ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="EditarAutor.php?id=<?= $id ?>">
                <label for="nome">Nome completo</label>
                <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($autor['nome']) ?>" required>

                <label for="nacionalidade">Nacionalidade</label>
                <input type="text" id="nacionalidade" name="nacionalidade" value="<?= htmlspecialchars($autor['nacionalidade'] ?? '') ?>" placeholder="Ex: Brasileiro, Francês...">

                <label for="nascimento">Data de nascimento</label>
                <input type="date" id="nascimento" name="nascimento" value="<?= htmlspecialchars($autor['nascimento'] ?? '') ?>">

                <button type="submit">Salvar alterações</button>
                <a href="Autores.php" class="link-cancelar">Cancelar e voltar</a>
            </form>
        </div>
    </main>

</body>
</html>