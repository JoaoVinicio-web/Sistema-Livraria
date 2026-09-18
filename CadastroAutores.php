<?php
session_start();
require_once 'conexao.php';

// Proteção da página: apenas usuários autenticados
if (!isset($_SESSION['usuario_id'])) {
    header('Location: Login.php');
    exit;
}

$mensagem = '';
$tipoMensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome          = trim($_POST['nome'] ?? '');
    $nascimento    = !empty($_POST['nascimento']) ? $_POST['nascimento'] : null;
    $nacionalidade = trim($_POST['nacionalidade'] ?? '');

    if (empty($nome)) {
        $mensagem = 'Por favor, informe o nome do autor.';
        $tipoMensagem = 'aviso';
    } else {
        try {
            $sql = "INSERT INTO autores (nome, nascimento, nacionalidade) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nome, $nascimento, $nacionalidade]);

            $mensagem = 'Autor cadastrado com sucesso!';
            $tipoMensagem = 'sucesso';
        } catch (PDOException $e) {
            $mensagem = 'Erro ao cadastrar autor: ' . $e->getMessage();
            $tipoMensagem = 'erro';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Autor - Livraria</title>
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
            <h1>Cadastrar autor</h1>

            <?php if (!empty($mensagem)): ?>
                <div class="alerta <?= $tipoMensagem ?>">
                    <?= $mensagem ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="CadastroAutores.php">
                <label for="nome">Nome completo</label>
                <input type="text" id="nome" name="nome" required>

                <label for="nacionalidade">Nacionalidade</label>
                <input type="text" id="nacionalidade" name="nacionalidade" placeholder="Ex: Brasileiro, Francês...">

                <label for="nascimento">Data de nascimento</label>
                <input type="date" id="nascimento" name="nascimento">

                <button type="submit">Cadastrar autor</button>
            </form>
        </div>
    </main>

</body>
</html>