<?php
session_start();

// Proteção da página: se não estiver logado, manda para o login
if (!isset($_SESSION['usuario_id'])) {
    header('Location: Login.php');
    exit;
}

$nomeUsuario = htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Livraria</title>
    <link rel="stylesheet" href="style.css">
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
                    <a href="Livro.php">Livros cadastrados</a>
                    <a href="CadastroLivro.php">Cadastrar livro</a>
                </div>
            </div>

            <div class="dropdown">
                <a href="#">Autores ▾</a>
                <div class="dropdown-menu">
                    <a href="Autores.php">Autores cadastrados</a>
                    <a href="CadastroAutores.php">Cadastrar autor</a>
                </div>
            </div>

            <a href="logout.php">Deslogar</a>
        </div>
    </nav>

    <main class="conteudo">
        <h1>Bem-vindo à Livraria, <?= $nomeUsuario ?>!</h1>

        <p>Gerencie os livros e autores cadastrados no sistema.</p>

        <div class="opcoes">
            <div class="card">
                <h2>Livros</h2>
                <p>Visualize ou cadastre novos livros.</p>
                <a href="Livro.php">Ver livros</a>
                <a href="CadastroLivro.php">Cadastrar livro</a>
            </div>

            <div class="card">
                <h2>Autores</h2>
                <p>Visualize ou cadastre novos autores.</p>
                <a href="Autores.php">Ver autores</a>
                <a href="CadastroAutores.php">Cadastrar autor</a>
            </div>
        </div>
    </main>

</body>
</html>