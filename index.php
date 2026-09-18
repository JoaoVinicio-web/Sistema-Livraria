<?php
session_start();

// Se o usuário já estiver autenticado, vai direto para a tela principal
if (isset($_SESSION['usuario_id'])) {
    header('Location: Principal.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Livraria</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link rel="stylesheet" href="style.css">
</head>

<body>

    <div class="container">
        <h1>Livraria</h1>

        <p>Bem-vindo à nossa livraria!</p>

        <div class="botoes">
            <a href="Login.php">Logar</a>
            <a href="Cadastro.php">Cadastrar</a>
        </div>
    </div>

</body>
</html>