<?php
session_start();
require_once 'conexao.php';

// Proteção da página
if (!isset($_SESSION['usuario_id'])) {
    header('Location: Login.php');
    exit;
}

$mensagem = '';
$tipoMensagem = '';

// Processamento do formulário
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
            $sql = "INSERT INTO livros (titulo, autor, editora, ano, quantidade, preco) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$titulo, $autor, $editora, $ano, $quantidade, $preco]);

            $mensagem = 'Livro cadastrado com sucesso! <a href="Livro.php">Ver lista de livros</a>';
            $tipoMensagem = 'sucesso';
        } catch (PDOException $e) {
            $mensagem = 'Erro ao cadastrar o livro: ' . $e->getMessage();
            $tipoMensagem = 'erro';
        }
    }
}

// Busca os autores cadastrados para popular o <select>
try {
    $stmtAutores = $pdo->query("SELECT id, nome FROM autores ORDER BY nome ASC");
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
    <title>Cadastrar Livro - Livraria</title>
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
            <h1>Cadastrar livro</h1>

            <?php if (!empty($mensagem)): ?>
                <div class="alerta <?= $tipoMensagem ?>">
                    <?= $mensagem ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="CadastroLivro.php">
                <label for="titulo">Título</label>
                <input type="text" id="titulo" name="titulo" required>

                <label for="autor">Autor</label>
                <select id="autor" name="autor" required>
                    <option value="">Selecione um autor</option>
                    <?php foreach ($autoresDisponiveis as $autorItem): ?>
                        <option value="<?= htmlspecialchars($autorItem['nome']) ?>">
                            <?= htmlspecialchars($autorItem['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="editora">Editora</label>
                <input type="text" id="editora" name="editora">

                <label for="ano">Ano de publicação</label>
                <input type="number" id="ano" name="ano" required>

                <label for="quantidade">Quantidade</label>
                <input type="number" id="quantidade" name="quantidade" min="0" value="0">

                <label for="preco">Preço (R$)</label>
                <input type="number" step="0.01" id="preco" name="preco" min="0" value="0.00">

                <button type="submit">Cadastrar livro</button>
            </form>
        </div>
    </main>

</body>
</html>