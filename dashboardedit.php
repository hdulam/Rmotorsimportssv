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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openEditModal(id, marca, modelo, anio, precio, kilometraje, tipo, transmision, imagen_url, estado) {
            $("#edit-id").val(id);
            $("#edit-marca").val(marca);
            $("#edit-modelo").val(modelo);
            $("#edit-anio").val(anio);
            $("#edit-precio").val(precio);
            $("#edit-kilometraje").val(kilometraje);
            $("#edit-tipo").val(tipo);
            $("#edit-transmision").val(transmision);
            $("#existing-image").val(imagen_url);
            $("#edit-estado").val(estado);
            $("#editModal").modal("show");
        }
    </script>
</head>
<body class="container mt-4">
    <h3 class="mb-4">Available Cars</h3>
    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Brand</th>
                <th>Model</th>
                <th>Year</th>
                <th>Price</th>
                <th>Mileage</th>
                <th>Type</th>
                <th>Transmission</th>
                <th>Image</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['marca']; ?></td>
                <td><?php echo $row['modelo']; ?></td>
                <td><?php echo $row['anio']; ?></td>
                <td><?php echo $row['precio']; ?></td>
                <td><?php echo $row['kilometraje']; ?></td>
                <td><?php echo $row['tipo']; ?></td>
                <td><?php echo $row['transmision']; ?></td>
                <td><img src="<?php echo $row['imagen_url']; ?>" width="100" class="img-thumbnail"></td>
                <td><?php echo $row['estado']; ?></td>
                <td>
                    <button class="btn btn-primary btn-sm" onclick="openEditModal(<?php echo $row['id']; ?>, '<?php echo $row['marca']; ?>', '<?php echo $row['modelo']; ?>', <?php echo $row['anio']; ?>, <?php echo $row['precio']; ?>, <?php echo $row['kilometraje']; ?>, '<?php echo $row['tipo']; ?>', '<?php echo $row['transmision']; ?>', '<?php echo $row['imagen_url']; ?>', '<?php echo $row['estado']; ?>')"><i class="fas fa-edit"></i></button>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="delete" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Car</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" id="edit-id" name="id">
                        <label>Brand:</label>
                        <select id="edit-marca" name="marca" class="form-control">
                            <?php foreach ($brands as $brand): ?>
                                <option value="<?php echo $brand; ?>"><?php echo $brand; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label>Model:</label>
                        <input type="text" id="edit-modelo" name="modelo" class="form-control">
                        <label>Year:</label>
                        <input type="number" id="edit-anio" name="anio" class="form-control">
                        <label>Price:</label>
                        <input type="text" id="edit-precio" name="precio" class="form-control">
                        <label>Upload Image:</label>
                        <input type="file" name="imagen" class="form-control">
                        <input type="hidden" id="existing-image" name="existing_image">
                        <button type="submit" name="edit" class="btn btn-success mt-3">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
