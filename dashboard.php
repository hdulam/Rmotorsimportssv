<?php
session_start();
require 'db.php';

// Asegúrate de que el usuario esté logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Opciones disponibles para los campos
$marcas = ['Toyota', 'Jeep', 'Honda', 'Ford', 'Chevrolet', 'BMW', 'Mercedes', 'Audi', 'Nissan', 'Hyundai', 'Volkswagen', 'Mitsubishi', 'Kia', 'Subaru'];
$tipos = ['Sedán', 'SUV', 'Camioneta', 'Coupé', 'Convertible', 'Hatchback', 'Familiar', 'Minivan', 'Pickup'];
$transmisiones = ['Standard', 'Automática'];
$estados = ['Liberado', 'En Camino', 'Vendido', 'En Aduana'];

// Lógica para agregar coche
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Manejar el agregar un coche nuevo
    if (isset($_POST['add'])) {
        $marca = $_POST['marca'];
        $modelo = $_POST['modelo'];
        $anio = $_POST['anio'];
        $precio = $_POST['precio'];
        $kilometraje = $_POST['kilometraje'];
        $tipo = $_POST['tipo'];
        $transmision = $_POST['transmision'];
        $estado = $_POST['estado'];
        $catWhatsapp = $_POST['catWhatsapp'];

        // Manejar la carga de la imagen
        $rutaImagen = '';
        if (!empty($_FILES['imagen']['name'])) {
            $directorioDestino = "uploads/";
            if (!is_dir($directorioDestino)) mkdir($directorioDestino, 0777, true);
            $rutaImagen = $directorioDestino . basename($_FILES["imagen"]["name"]);
            move_uploaded_file($_FILES["imagen"]["tmp_name"], $rutaImagen);
        }

        // Insertar los datos del coche en la base de datos
        $sql = "INSERT INTO autos (marca, modelo, anio, precio, kilometraje, tipo, transmision, imagen_url, estado, catWhatsapp) 
                VALUES ('$marca', '$modelo', '$anio', '$precio', '$kilometraje', '$tipo', '$transmision', '$rutaImagen', '$estado', '$catWhatsapp')";
        $conn->query($sql);
        header("Location: dashboard.php");
    }

    // Manejar la eliminación de un coche
    if (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $sql = "DELETE FROM autos WHERE id=$id";
        $conn->query($sql);
        header("Location: dashboard.php");
    }

    // Manejar la edición de un coche
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
        $catWhatsapp = $_POST['catWhatsapp'];

        // Manejar la carga de la imagen al editar
        $rutaImagen = $_POST['existing_image'];
        if (!empty($_FILES['imagen']['name'])) {
            $directorioDestino = "uploads/";
            if (!is_dir($directorioDestino)) mkdir($directorioDestino, 0777, true);
            $rutaImagen = $directorioDestino . basename($_FILES["imagen"]["name"]);
            move_uploaded_file($_FILES["imagen"]["tmp_name"], $rutaImagen);
        }

        // Actualizar los datos del coche en la base de datos
        $sql = "UPDATE autos SET marca='$marca', modelo='$modelo', anio='$anio', precio='$precio', kilometraje='$kilometraje', tipo='$tipo', transmision='$transmision', imagen_url='$rutaImagen', estado='$estado', catWhatsapp='$catWhatsapp' WHERE id=$id";
        $conn->query($sql);
        header("Location: dashboard.php");
    }
}

// Recuperar todos los coches de la base de datos
$result = $conn->query("SELECT * FROM autos");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inventario de Autos</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Función para abrir el modal de edición con los datos del coche
        function openEditModal(id, marca, modelo, anio, precio, kilometraje, tipo, transmision, imagen_url, estado, catWhatsapp) {
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
            $("#edit-catWhatsapp").val(catWhatsapp);
            $("#editModal").modal("show");
        }
    </script>
</head>
<body class="container mt-4">
    <a href="logout.php"><h4>Cerrar Sesión</h4></a>
    <h3 class="mb-4">Inventario de Autos</h3>

    <!-- Tabla de Coches -->
    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Año</th
                <th>Precio</th>
                <th>Millas</th>
                <th>Tipo</th>
                <th>Transmisión</th>
                <th>Imagen</th>
                <th>Estado</th>
                <th>Acciones</th>
                <th>Whatsapp</th>
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
                    <button class="btn btn-primary btn-sm" onclick="openEditModal(<?php echo $row['id']; ?>, '<?php echo $row['marca']; ?>', '<?php echo $row['modelo']; ?>', <?php echo $row['anio']; ?>, <?php echo $row['precio']; ?>, <?php echo $row['kilometraje']; ?>, '<?php echo $row['tipo']; ?>', '<?php echo $row['transmision']; ?>', '<?php echo $row['imagen_url']; ?>', '<?php echo $row['estado']; ?>', '<?php echo $row['catWhatsapp'];?>')"><i class="fas fa-edit"></i></button>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="delete" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
                <td><?php echo $row['catWhatsapp']; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Formulario para Agregar Nuevo Coche -->
    <h3>Agregar Nuevo Coche</h3>
    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="marca">Marca</label>
            <select name="marca" class="form-control" required>
                <?php foreach ($marcas as $marca): ?>
                    <option value="<?php echo $marca; ?>"><?php echo $marca; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="modelo">Modelo</label>
            <input type="text" name="modelo" class="form-control" placeholder="Modelo" required>
        </div>
        <div class="form-group">
            <label for="anio">Año</label>
            <input type="number" name="anio" class="form-control" placeholder="Año" required>
        </div>
        <div class="form-group">
            <label for="precio">Precio</label>
            <input type="number" step="0.01" name="precio" class="form-control" placeholder="Precio" required>
        </div>
        <div class="form-group">
            <label for="kilometraje">Millas</label>
            <input type="number" name="kilometraje" class="form-control" placeholder="Millas" required>
        </div>
        <div class="form-group">
            <label for="tipo">Tipo</label>
            <select name="tipo" class="form-control" required>
                <?php foreach ($tipos as $tipo): ?>
                    <option value="<?php echo $tipo; ?>"><?php echo $tipo; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="transmision">Transmisión</label>
            <select name="transmision" class="form-control" required>
                <?php foreach ($transmisiones as $transmision): ?>
                    <option value="<?php echo $transmision; ?>"><?php echo $transmision; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="imagen">Imagen</label>
            <input type="file" name="imagen" accept="image/*" class="form-control">
        </div>
        <div class="form-group">
            <label for="estado">Estado</label>
            <select name="estado" class="form-control" required>
                <?php foreach ($estados as $estado): ?>
                    <option value="<?php echo $estado; ?>"><?php echo $estado; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="catWhatsapp">Whatsapp</label>
            <input type="text" name="catWhatsapp" class="form-control" placeholder="Whatsapp Catalogo" required>
        </div>
        <button type="submit" name="add" class="btn btn-success mt-3">Agregar Auto</button>
    </form>

    <!-- Modal para Editar Auto -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Auto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" id="edit-id" name="id">
                        <div class="form-group">
                            <label for="edit-marca">Marca</label>
                            <select id="edit-marca" name="marca" class="form-control" required>
                                <?php foreach ($marcas as $marca): ?>
                                    <option value="<?php echo $marca; ?>"><?php echo $marca; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit-modelo">Modelo</label>
                            <input type="text" id="edit-modelo" name="modelo" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-anio">Año</label>
                            <input type="number" id="edit-anio" name="anio" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-precio">Precio</label>
                            <input type="number" step="0.01" id="edit-precio" name="precio" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-kilometraje">Millas</label>
                            <input type="number" id="edit-kilometraje" name="kilometraje" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-tipo">Tipo</label>
                            <select id="edit-tipo" name="tipo" class="form-control" required>
                                <?php foreach ($tipos as $tipo): ?>
                                    <option value="<?php echo $tipo; ?>"><?php echo $tipo; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit-transmision">Transmisión</label>
                            <select id="edit-transmision" name="transmision" class="form-control" required>
                                <?php foreach ($transmisiones as $transmision): ?>
                                    <option value="<?php echo $transmision; ?>"><?php echo $transmision; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit-estado">Estado</label>
                            <select id="edit-estado" name="estado" class="form-control" required>
                                <?php foreach ($estados as $estado): ?>
                                    <option value="<?php echo $estado; ?>"><?php echo $estado; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="imagen">Imagen</label>
                            <input type="file" name="imagen" class="form-control">
                            <input type="hidden" name="existing_image" id="existing-image">
                        </div>
                        <div class="form-group">
                            <label for="edit-catWhatsapp">Whatsapp</label>
                            <input type="text" id="edit-catWhatsapp" name="catWhatsapp" class="form-control" required>
                        </div>
                        <button type="submit" name="edit" class="btn btn-warning mt-3">Actualizar Auto</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
