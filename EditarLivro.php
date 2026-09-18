<?php
session_start();
require_once 'conexao.php';

// Proteção da página
if (!isset($_SESSION['usuario_id'])) {
    header('Location: Login.php');
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Se não houver ID válido, redireciona para a lista de livros
if (!$id) {
    header('Location: Livro.php');
    exit;
}

$mensagem = '';
$tipoMensagem = '';

// Processa a atualização dos dados do livro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo     = trim($_POST['titulo'] ?? '');
    $autor      = trim($_POST['autor'] ?? '');
    $editora    = trim($_POST['editora'] ?? '');
    $ano        = !empty($_POST['ano']) ? (int)$_POST['ano'] : null;
    $quantidade = isset($_POST['quantidade']) && $_POST['quantidade'] !== '' ? (int)$_POST['quantidade'] : 0;
    $preco      = isset($_POST['preco']) && $_POST['preco'] !== '' ? (float)$_POST['preco'] : 0.00;

    if (empty($titulo) || empty($autor) || empty($ano)) {
        $mensagem = 'Preencha ao menos o título, o autor e o ano de publicação.';
        $tipoMensagem = 'aviso';
    } else {
        try {
            $sqlUpdate = "UPDATE livros SET titulo = ?, autor = ?, editora = ?, ano = ?, quantidade = ?, preco = ? WHERE id = ?";
            $stmtUpdate = $pdo->prepare($sqlUpdate);
            $stmtUpdate->execute([$titulo, $autor, $editora, $ano, $quantidade, $preco, $id]);

            $mensagem = 'Livro atualizado com sucesso! <a href="Livro.php">Voltar para a lista</a>';
            $tipoMensagem = 'sucesso';
        } catch (PDOException $e) {
            $mensagem = 'Erro ao atualizar o livro: ' . $e->getMessage();
            $tipoMensagem = 'erro';
        }
    }
}

// Busca os dados do livro selecionado
try {
    $stmtLivro = $pdo->prepare("SELECT * FROM livros WHERE id = ?");
    $stmtLivro->execute([$id]);
    $livro = $stmtLivro->fetch();

    if (!$livro) {
        header('Location: Livro.php');
        exit;
    }
} catch (PDOException $e) {
    die("Erro ao carregar dados do livro: " . $e->getMessage());
}

// Busca os autores cadastrados para o <select>
try {
    $stmtAutores = $pdo->query("SELECT nome FROM autores ORDER BY nome ASC");
    $autoresDisponiveis = $stmtAutores->fetchAll();
} catch (PDOException $e) {
    die("Erro ao carregar autores: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Livro - Livraria</title>
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
            <h1>Editar livro</h1>

            <?php if (!empty($mensagem)): ?>
                <div class="alerta <?= $tipoMensagem ?>">
                    <?= $mensagem ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="EditarLivro.php?id=<?= $id ?>">
                <label for="titulo">Título</label>
                <input type="text" id="titulo" name="titulo" value="<?= htmlspecialchars($livro['titulo']) ?>" required>

                <label for="autor">Autor</label>
                <select id="autor" name="autor" required>
                    <option value="">Selecione um autor</option>
                    <?php foreach ($autoresDisponiveis as $autorItem): ?>
                        <option value="<?= htmlspecialchars($autorItem['nome']) ?>" <?= ($livro['autor'] === $autorItem['nome']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($autorItem['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="editora">Editora</label>
                <input type="text" id="editora" name="editora" value="<?= htmlspecialchars($livro['editora'] ?? '') ?>">

                <label for="ano">Ano de publicação</label>
                <input type="number" id="ano" name="ano" value="<?= htmlspecialchars($livro['ano']) ?>" required>

                <label for="quantidade">Quantidade</label>
                <input type="number" id="quantidade" name="quantidade" min="0" value="<?= htmlspecialchars($livro['quantidade'] ?? 0) ?>">

                <label for="preco">Preço (R$)</label>
                <input type="number" step="0.01" id="preco" name="preco" min="0" value="<?= htmlspecialchars($livro['preco'] ?? '0.00') ?>">

                <button type="submit">Salvar alterações</button>
                <a href="Livro.php" class="link-cancelar">Cancelar e voltar</a>
            </form>
        </div>
    </main>

</body>
</html>