<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Tareas</title>
</head>
<body>
    <h1>Mis Tareas</h1>

    <!-- Lista generada por PHP -->
    <div id="lista-tareas">
        <?php include("proyecto.php"); ?>
    </div>

    <!-- Formulario -->
    <form method="post" action="proyecto.php">
        <input type="text" name="nueva_tarea" placeholder="Escribe una tarea" required>
        <button type="submit">Agregar</button>
    </form>
</body>
</html>
