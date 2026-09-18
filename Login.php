<?php
session_start();
require_once 'conexao.php';

// Se o usuário já estiver logado, redireciona direto para a página principal
if (isset($_SESSION['usuario_id'])) {
    header('Location: Principal.php');
    exit;
}

$mensagem = '';
$tipoMensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $senha   = $_POST['senha'] ?? '';

    if (empty($usuario) || empty($senha)) {
        $mensagem = 'Preencha todos os campos!';
        $tipoMensagem = 'warning';
    } else {
        try {
            // Busca o usuário pelo nome de usuário ou e-mail
            $stmt = $pdo->prepare("SELECT id, nome, usuario, senha FROM usuarios WHERE usuario = ? OR email = ? LIMIT 1");
            $stmt->execute([$usuario, $usuario]);
            $user = $stmt->fetch();

            // Verifica se o usuário existe e valida o hash da senha
            if ($user && password_verify($senha, $user['senha'])) {
                // Guarda dados essenciais na sessão
                $_SESSION['usuario_id']   = $user['id'];
                $_SESSION['usuario_nome'] = $user['nome'];
                $_SESSION['usuario_login'] = $user['usuario'];

                header('Location: Principal.php');
                exit;
            } else {
                $mensagem = 'Usuário ou senha inválidos!';
                $tipoMensagem = 'danger';
            }
        } catch (PDOException $e) {
            $mensagem = 'Erro no servidor: ' . $e->getMessage();
            $tipoMensagem = 'danger';
        }
    }
}
?>
<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - Sistema Livraria</title>
    
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
      crossorigin="anonymous"
    ></script>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
      crossorigin="anonymous"
    />
    <link rel="stylesheet" href="style.css" />
  </head>
  <body>
    <div class="container mt-5">
        <h2>Login</h2>
        <p>Ainda não é cadastrado? <a href="Cadastro.php">Cadastrar</a></p>

        <?php if (!empty($mensagem)): ?>
            <div class="alert alert-<?= $tipoMensagem ?>" role="alert">
                <?= $mensagem ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="Login.php">
            <div class="mb-3">
                <input type="text" placeholder="Usuário ou E-mail" class="form-control" id="usuario" name="usuario" required>
            </div>

            <div class="mb-3">
                <input type="password" placeholder="Senha" class="form-control" id="senha" name="senha" required>
            </div>

            <button type="submit" class="btn btn-primary">Entrar</button>
        </form>
    </div>
  </body>
</html>