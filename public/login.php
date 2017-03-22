<?php

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Ingreso</title>
    <link rel="stylesheet" type="text/css" href="css/vivaStyle.css" media="screen" />
</head>
<body>
<div class="login-page">
<h1>Sistema VIVA-Ingreso</h1>
<?php if (isset($loginError)): ?>
    <p><?php echo($loginError); ?></p>
<?php endif; ?>

    <form action="" method="post" class="form">
        <div>
            <label for="usuario">Usuario: <input type="text" name="usuario" id="usuario"></label>
        </div>
        <div>
            <label for="password">Clave: <input type="password" name="password" id="password"></label>
        </div>
        <div>
            <input type="hidden" name="action" value="Ingresar">
            <input type="submit" value="Login" id="btnLogin">
        </div>
    </form>
</div>
</body>
</html>