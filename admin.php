<?php
session_start();

// Configuración de la base de datos
$host = 'localhost';
$dbname = 'your_existing_database';
$username = 'tu_usuario';
$password = 'tu_contraseña';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Autenticación básica
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Agregar, modificar o eliminar autos
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $marca = $_POST['marca'];
        $modelo = $_POST['modelo'];
        $anio = $_POST['anio'];
        $precio = $_POST['precio'];
        $estado = $_POST['estado'];
        
        $stmt = $pdo->prepare("INSERT INTO autos (marca, modelo, anio, precio, estado) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$marca, $modelo, $anio, $precio, $estado]);
    }
    if (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM autos WHERE id = ?");
        $stmt->execute([$id]);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administración de Autos</title>
</head>
<body>
    <h2>Portal de Administración</h2>
    <a href="logout.php">Cerrar Sesión</a>
    
    <h3>Agregar Auto</h3>
    <form method="post">
        <input type="text" name="marca" placeholder="Marca" required>
        <input type="text" name="modelo" placeholder="Modelo" required>
        <input type="number" name="anio" placeholder="Año" required>
        <input type="number" name="precio" placeholder="Precio" required>
        <select name="estado">
            <option value="Disponible">Disponible</option>
            <option value="Reservado">Reservado</option>
            <option value="Vendido">Vendido</option>
        </select>
        <button type="submit" name="add">Agregar</button>
    </form>
    
    <h3>Lista de Autos</h3>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Marca</th>
            <th>Modelo</th>
            <th>Año</th>
            <th>Precio</th>
            <th>Estado</th>
            <th>Acción</th>
        </tr>
        <?php
        $stmt = $pdo->query("SELECT * FROM autos");
        while ($row = $stmt->fetch()) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['marca']}</td>
                    <td>{$row['modelo']}</td>
                    <td>{$row['anio']}</td>
                    <td>\${$row['precio']}</td>
                    <td>{$row['estado']}</td>
                    <td>
                        <form method='post'>
                            <input type='hidden' name='id' value='{$row['id']}'>
                            <button type='submit' name='delete'>Eliminar</button>
                        </form>
                    </td>
                  </tr>";
        }
        ?>
    </table>
</body>
</html>

<?php
// login.php (Archivo separado)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['user'];
    $pass = $_POST['pass'];
    
    if ($user === 'admin' && $pass === '1234') {
        $_SESSION['user'] = $user;
        header("Location: admin.php");
        exit();
    } else {
        $error = "Usuario o contraseña incorrectos";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <h2>Iniciar Sesión</h2>
    <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
    <form method="post">
        <input type="text" name="user" placeholder="Usuario" required>
        <input type="password" name="pass" placeholder="Contraseña" required>
        <button type="submit">Entrar</button>
    </form>
</body>
</html>

<?php
// logout.php
session_start();
session_destroy();
header("Location: login.php");
exit();
?>
