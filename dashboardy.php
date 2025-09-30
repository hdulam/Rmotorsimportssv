<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$brands = ['Toyota', 'Honda', 'Ford', 'Chevrolet', 'BMW', 'Mercedes', 'Audi', 'Nissan', 'Hyundai', 'Volkswagen'];
$types = ['Sedan', 'SUV', 'Truck', 'Coupe', 'Convertible', 'Hatchback', 'Wagon', 'Van'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add'])) {
        $marca = $_POST['marca'];
        $modelo = $_POST['modelo'];
        $anio = $_POST['anio'];
        $precio = $_POST['precio'];
        $kilometraje = $_POST['kilometraje'];
        $tipo = $_POST['tipo'];
        $transmision = $_POST['transmision'];
        $estado = $_POST['estado'];
        
        $imagePath = '';
        if (!empty($_FILES['imagen']['name'])) {
            $targetDir = "uploads/";
            if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
            $imagePath = $targetDir . basename($_FILES["imagen"]["name"]);
            move_uploaded_file($_FILES["imagen"]["tmp_name"], $imagePath);
        }
        
        $sql = "INSERT INTO autos (marca, modelo, anio, precio, kilometraje, tipo, transmision, imagen_url, estado) 
                VALUES ('$marca', '$modelo', '$anio', '$precio', '$kilometraje', '$tipo', '$transmision', '$imagePath', '$estado')";
        $conn->query($sql);
        header("Location: dashboard.php");
    }
    
    if (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $sql = "DELETE FROM autos WHERE id=$id";
        $conn->query($sql);
        header("Location: dashboard.php");
    }
    
    if (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $marca = $_POST['marca'];
        $modelo = $_POST['modelo'];
        $anio = $_POST['anio'];
        $precio = $_POST['precio'];
        $kilometraje = $_POST['kilometraje'];
        $tipo = $_POST['tipo'];
        $transmision = $_POST['transmision'];
        $estado = $_POST['estado'];
        
        $imagePath = $_POST['existing_image'];
        if (!empty($_FILES['imagen']['name'])) {
            $targetDir = "uploads/";
            if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
            $imagePath = $targetDir . basename($_FILES["imagen"]["name"]);
            move_uploaded_file($_FILES["imagen"]["tmp_name"], $imagePath);
        }
        
        $sql = "UPDATE autos SET marca='$marca', modelo='$modelo', anio='$anio', precio='$precio', kilometraje='$kilometraje', tipo='$tipo', transmision='$transmision', imagen_url='$imagePath', estado='$estado' WHERE id=$id";
        $conn->query($sql);
        header("Location: dashboard.php");
    }
}

$result = $conn->query("SELECT * FROM autos");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            margin: 0;
            padding: 20px;
        }
        h2 {
            color: #333;
        }
        table {
            width: 80%;
            margin: auto;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background: #007BFF;
            color: white;
        }
        button {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            border-radius: 5px;
        }
        button:hover {
            background: #c82333;
        }
        .add-form input, select, button {
            padding: 10px;
            margin: 5px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .add-form button {
            background: #28a745;
            color: white;
            border: none;
        }
        .add-form button:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <h2>Welcome, <?php echo $_SESSION['username']; ?>!</h2>
    <a href="logout.php" style="color: red; text-decoration: none;">Logout</a>
    
    <h3>Add New Car</h3>
    <form class="add-form" method="post" enctype="multipart/form-data">
        <select name="marca"> <?php foreach ($brands as $brand) echo "<option>$brand</option>"; ?> </select>
        <input type="text" name="modelo" placeholder="Model" required>
        <input type="number" name="anio" placeholder="Year" required>
        <input type="number" step="0.01" name="precio" placeholder="Price" required>
        <input type="number" name="kilometraje" placeholder="Mileage" required>
        <select name="tipo"> <?php foreach ($types as $type) echo "<option>$type</option>"; ?> </select>
        <select name="transmision">
            <option>Manual</option>
            <option>Automatica</option>
        </select>
        <input type="file" name="imagen" accept="image/*">
        <select name="estado">
            <option>Disponible</option>
            <option>Reservado</option>
            <option>Vendido</option>
        </select>
        <button type="submit" name="add">Add Car</button>
    </form>
</body>
</html>
