<?php

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Ingreso</title>
</head>
<body>
<h1>ingreso</h1>
<p>Por favor ingrese su usuario y clave.</p>
<?php if (isset($loginError)): ?>
    <p><?php echo($loginError); ?></p>
<?php endif; ?>
<form action="" method="post">
    <div>
        <label for="usuario">Usuario: <input type="text" name="usuario" id="usuario"></label>
    </div>
    <div>
        <label for="password">Clave: <input type="password" name="password" id="password"></label>
    </div>
    <div>
        <input type="hidden" name="action" value="Ingresar">
        <input type="submit" value="Log in">
    </div>
</form>
</body>
</html>