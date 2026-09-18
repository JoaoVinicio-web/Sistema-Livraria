<?php
require_once 'conexao.php';

$mensagem = '';
$tipoMensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome    = trim($_POST['nome'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $usuario = trim($_POST['usuario'] ?? '');
    $senha   = $_POST['senha'] ?? '';

    // Validação básica dos campos
    if (empty($nome) || empty($email) || empty($usuario) || empty($senha)) {
        $mensagem = 'Preencha todos os campos!';
        $tipoMensagem = 'danger';
    } else {
        try {
            // Verifica se o usuário ou e-mail já estão cadastrados
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ? OR usuario = ? LIMIT 1");
            $stmt->execute([$email, $usuario]);
            
            if ($stmt->rowCount() > 0) {
                $mensagem = 'E-mail ou nome de usuário já cadastrado!';
                $tipoMensagem = 'warning';
            } else {
                // Criptografa a senha com segurança
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

                // Insere no banco de dados
                $sql = "INSERT INTO usuarios (nome, email, usuario, senha) VALUES (?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nome, $email, $usuario, $senhaHash]);

                $mensagem = 'Cadastro realizado com sucesso! <a href="Login.php" class="alert-link">Clique aqui para logar</a>.';
                $tipoMensagem = 'success';
            }
        } catch (PDOException $e) {
            $mensagem = 'Erro ao cadastrar: ' . $e->getMessage();
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
    <title>Cadastro - Sistema Livraria</title>
    
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
        <h2>Cadastro</h2>
        <p>Já é cadastrado? <a href="Login.php">Logar</a></p>

        <?php if (!empty($mensagem)): ?>
            <div class="alert alert-<?= $tipoMensagem ?>" role="alert">
                <?= $mensagem ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="Cadastro.php">
            <div class="mb-3">
                <input type="text" placeholder="Nome" class="form-control" id="nome" name="nome" required>
            </div>

            <div class="mb-3">
                <input type="email" placeholder="E-mail" class="form-control" id="email" name="email" required>
            </div>

            <div class="mb-3">
                <input type="text" placeholder="Usuário" class="form-control" id="usuario" name="usuario" required>
            </div>

            <div class="mb-3">
                <input type="password" placeholder="Senha" class="form-control" id="senha" name="senha" required>
            </div>

            <button type="submit" class="btn btn-primary">Cadastrar</button>
        </form>
    </div>
  </body>
</html>