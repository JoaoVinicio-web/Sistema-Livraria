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

// Lógica de exclusão de livro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir_id'])) {
    $idParaExcluir = (int)$_POST['excluir_id'];
    try {
        $stmtDelete = $pdo->prepare("DELETE FROM livros WHERE id = ?");
        $stmtDelete->execute([$idParaExcluir]);
        $mensagem = 'Livro excluído com sucesso!';
        $tipoMensagem = 'sucesso';
    } catch (PDOException $e) {
        $mensagem = 'Erro ao excluir livro: ' . $e->getMessage();
        $tipoMensagem = 'erro';
    }
}

// Busca todos os livros cadastrados
try {
    $stmt = $pdo->query("SELECT * FROM livros ORDER BY id DESC");
    $livros = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erro ao consultar os livros: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livros - Livraria</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .alerta {
            padding: 12px;
            margin: 15px auto;
            border-radius: 4px;
            text-align: center;
            max-width: 900px;
        }
        .alerta.sucesso { background-color: #d1e7dd; color: #0f5132; }
        .alerta.erro { background-color: #f8d7da; color: #842029; }
        .form-inline { display: inline; margin: 0; }
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

        <h1>Livros cadastrados</h1>

        <?php if (!empty($mensagem)): ?>
            <div class="alerta <?= $tipoMensagem ?>">
                <?= $mensagem ?>
            </div>
        <?php endif; ?>

        <a class="botao-principal" href="CadastroLivro.php">
            Cadastrar novo livro
        </a>

        <div class="tabela-container">
            <table>
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Título</th>
                        <th>Autor</th>
                        <th>Editora</th>
                        <th>Ano</th>
                        <th>Quantidade</th>
                        <th>Ações</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (count($livros) > 0): ?>
                        <?php foreach ($livros as $livro): ?>
                            <tr>
                                <td><?= str_pad($livro['id'], 3, '0', STR_PAD_LEFT) ?></td>
                                <td><?= htmlspecialchars($livro['titulo']) ?></td>
                                <td><?= htmlspecialchars($livro['autor']) ?></td>
                                <td><?= htmlspecialchars($livro['editora'] ?? 'Não informada') ?></td>
                                <td><?= htmlspecialchars($livro['ano']) ?></td>
                                <td><?= htmlspecialchars($livro['quantidade'] ?? 0) ?></td>
                                <td>
                                    <!-- Botão de Editar -->
                                    <a href="EditarLivro.php?id=<?= $livro['id'] ?>">
                                        <button type="button" class="botao-editar">Editar</button>
                                    </a>

                                    <!-- Botão de Excluir -->
                                    <form method="POST" action="Livro.php" class="form-inline" onsubmit="return confirm('Tem certeza que deseja excluir este livro?');">
                                        <input type="hidden" name="excluir_id" value="<?= $livro['id'] ?>">
                                        <button type="submit" class="botao-excluir">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center;">Nenhum livro cadastrado no momento.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </main>

</body>
</html>