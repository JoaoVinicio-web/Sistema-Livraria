<?php
session_start();
require_once 'conexao.php';

// Proteção de acesso
if (!isset($_SESSION['usuario_id'])) {
    header('Location: Login.php');
    exit;
}

$mensagem = '';
$tipoMensagem = '';

// Lógica de exclusão de autor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir_id'])) {
    $idParaExcluir = (int)$_POST['excluir_id'];
    try {
        $stmtDelete = $pdo->prepare("DELETE FROM autores WHERE id = ?");
        $stmtDelete->execute([$idParaExcluir]);
        $mensagem = 'Autor excluído com sucesso!';
        $tipoMensagem = 'sucesso';
    } catch (PDOException $e) {
        $mensagem = 'Erro ao excluir autor: ' . $e->getMessage();
        $tipoMensagem = 'erro';
    }
}

// Busca todos os autores cadastrados
try {
    $stmt = $pdo->query("SELECT * FROM autores ORDER BY nome ASC");
    $autores = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erro ao buscar autores: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autores - Livraria</title>
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

        <h1>Autores cadastrados</h1>

        <?php if (!empty($mensagem)): ?>
            <div class="alerta <?= $tipoMensagem ?>">
                <?= $mensagem ?>
            </div>
        <?php endif; ?>

        <a class="botao-principal" href="CadastroAutores.php">
            Cadastrar novo autor
        </a>

        <div class="tabela-container">
            <table>
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nome</th>
                        <th>Nacionalidade</th>
                        <th>Data de nascimento</th>
                        <th>Ações</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (count($autores) > 0): ?>
                        <?php foreach ($autores as $autor): ?>
                            <tr>
                                <td><?= str_pad($autor['id'], 3, '0', STR_PAD_LEFT) ?></td>
                                <td><?= htmlspecialchars($autor['nome']) ?></td>
                                <td><?= htmlspecialchars($autor['nacionalidade'] ?? 'Não informada') ?></td>
                                <td>
                                    <?= !empty($autor['nascimento']) ? date('d/m/Y', strtotime($autor['nascimento'])) : 'Não informada' ?>
                                </td>
                                <td>
                                    <!-- Botão de Editar -->
                                    <a href="EditarAutor.php?id=<?= $autor['id'] ?>">
                                        <button type="button" class="botao-editar">Editar</button>
                                    </a>

                                    <!-- Botão de Excluir -->
                                    <form method="POST" action="Autores.php" class="form-inline" onsubmit="return confirm('Tem certeza que deseja excluir este autor?');">
                                        <input type="hidden" name="excluir_id" value="<?= $autor['id'] ?>">
                                        <button type="submit" class="botao-excluir">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center;">Nenhum autor cadastrado ainda.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </main>

</body>
</html>