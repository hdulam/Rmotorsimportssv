<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'db.php';

// Initialize the filter values
$marca = isset($_GET['marca']) ? $_GET['marca'] : '';
$precio = isset($_GET['precio']) ? $_GET['precio'] : '';
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';
$transmision = isset($_GET['transmision']) ? $_GET['transmision'] : '';
$estado = isset($_GET['estado']) ? $_GET['estado'] : '';

// Create the base SQL query
$sql = "SELECT id, marca, modelo, anio, kilometraje, precio, imagen_url, tipo, transmision, estado, catWhatsapp FROM autos WHERE 1";

// Add filters to the query based on the selected options
if ($marca) {
    $sql .= " AND marca = '$marca'";
}


if ($precio) {
    //compara valor GET de formulario con base de datos
    if ($precio == '0-5000') {
        $sql .= " AND precio <= 5000";
    } elseif ($precio == '5000-10000') {
        $sql .= " AND precio BETWEEN 5000 AND 10000";
    } elseif ($precio == '10000-25000') {
        $sql .= " AND precio BETWEEN 10000 AND 25000";
    } elseif($precio == '25000+'){
        $sql .= " AND precio > 25000";
    }
}
if ($tipo) {
    $sql .= " AND tipo = '$tipo'";
}
if ($transmision) {
    $sql .= " AND transmision = '$transmision'";
}
if ($estado) {
    $sql .= " AND estado = '$estado'";
}

$result = $conn->query($sql);

// Check if results are available
$cars = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $cars[] = $row;
    }
} 

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RMotors Imports</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .bienvenida-section {
            background: linear-gradient(to right, #007bff, #00bfff);
            color: white;
            width: 100%;
            min-height: 50vh;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 0 20px;
        }

        .bienvenida-section h2 {
            font-size: 4rem;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .bienvenida-section p {
            font-size: 1.5rem;
            line-height: 1.8;
            max-width: 800px;
            margin: 0 auto;
        }

        .btn-custom {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            padding: 12px 40px;
            border-radius: 5px;
            transition: background-color 0.3s;
            font-size: 1.1rem;
        }

        .btn-custom:hover {
            background-color: #0056b3;
        }

        .section-header h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #007bff;
        }

        .car-card {
            margin-bottom: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
            background-color: white;
            transition: transform 0.3s ease-in-out;
        }

        .car-card:hover {
            transform: translateY(-10px);
        }

        .car-img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 10px;
        }

        .car-info {
            padding: 15px;
            text-align: center;
        }

        .car-info h5 {
            font-size: 1.25rem;
            margin: 10px 0;
        }

        .car-details {
            margin-top: 15px;
            font-size: 0.9rem;
            color: #6c757d;
        }

        .car-details span {
            margin-right: 10px;
        }

        footer {
            background-color: #343a40;
            color: white;
            padding: 20px 0;
            text-align: center;
        }

        .footer-links a {
            color: white;
            margin: 0 15px;
            text-decoration: none;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        .banner {
            width: 100vw;
            height: 80vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: url('bg.jpg') no-repeat center center/cover;
            position: relative;
        }
        .overlay {
            background: rgba(255, 255, 255, 0.7);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            position: absolute;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .overlay img {
            max-width: 300px;
            height: auto;
        }
        .overlay h1 {
            font-size: 2rem;
            color: #333;
            margin-top: 10px;
        }
        .history-img{
            text-align: center;
        }
    </style>
</head>
<body>
<div class="banner">
        <div class="overlay">
            <img src="logo.png" alt="RM Motors Imports Logo">
            <h1>Somos una empresa líder en la importación de vehículos en El Salvador, brindando un servicio confiable basado en  profesionalismo, honestidad y compromiso.</h1>
            <div class="mt-3">
    <a href="https://www.facebook.com/share/124SWyStJZ2/?mibextid=wwXIfr" class="btn btn-primary mx-2">
        <i class="fab fa-facebook-f"></i> Facebook
    </a>
    <a href="https://wa.me/c/50377409363" class="btn btn-success mx-2">
        <i class="fab fa-whatsapp"></i> Whatsapp
    </a>
    <a href="https://www.instagram.com/rmotorsimports/" class="btn btn-danger mx-2">
        <i class="fab fa-instagram"></i> Instagram
    </a>
</div>
        </div>
    </div>

    <!-- Catalogo con Filtros -->
    <div class="container mt-4" id="search">
        <div class="section-header">
            <center>
            <h2>Encuentra tu auto ideal</h2>
            </center>
        </div>
        <form class="row g-3" method="GET" action="">

            <div class="col-md-3">
                <label for="marca" class="form-label">Marca</label>
                <?php
                    $marcas = ['Toyota', 'Honda', 'Ford', 'Chevrolet', 'BMW', 'Mercedes', 'Audi', 'Nissan', 'Hyundai', 'Volkswagen', 'Mitsubishi', 'Kia'];
                ?>
                <select class="form-select" id="marca" name="marca">
                    <option value="">Selecciona una marca</option>
                    <?php foreach ($marcas as $m): ?>
                        <option value="<?= $m ?>" <?= ($marca == $m) ? 'selected' : '' ?>><?= $m ?></option>
                    <?php endforeach; ?>
            </select>
            </div>

            <div class="col-md-3">
                <label for="precio" class="form-label">Rango de precio</label>
                <select class="form-select" id="precio" name="precio">
                    <option value="">Selecciona un rango</option>
                    <option value="0-5000" <?= ($precio == '0-5000') ? 'selected' : '' ?>>Hasta $5,000</option>
                    <option value="5000-10000" <?= ($precio == '5000-10000') ? 'selected' : '' ?>>$5,000 - $10,000</option>
                    <option value="10000-25000" <?= ($precio == '10000-25000') ? 'selected' : '' ?>>$10,000 - $25,000</option>
                    <option value="25000+" <?= ($precio == '25000+') ? 'selected' : '' ?>>Más de $25,000</option>

                </select>
            </div>

            <div class="col-md-3">
                <label for="tipo" class="form-label">Tipo de auto</label>
                <select class="form-select" id="tipo" name="tipo">
                    <option value="">Selecciona un tipo</option>
                    <option value="Sedán" <?= ($tipo == 'Sedán') ? 'selected' : '' ?>>Sedán</option>
                    <option value="SUV" <?= ($tipo == 'SUV') ? 'selected' : '' ?>>SUV</option>
                    <option value="Camioneta" <?= ($tipo == 'Camioneta') ? 'selected' : '' ?>>Camioneta</option>
                    <option value="Coupé" <?= ($tipo == 'Coupé') ? 'selected' : '' ?>>Coupé</option>
                    <option value="Convertible" <?= ($tipo == 'Convertible') ? 'selected' : '' ?>>Convertible</option>
                    <option value="Hatchback" <?= ($tipo == 'Hatchback') ? 'selected' : '' ?>>Hatchback</option>
                    <option value="Familiar" <?= ($tipo == 'Familiar') ? 'selected' : '' ?>>Familiar</option>
                    <option value="Minivan" <?= ($tipo == 'Minivan') ? 'selected' : '' ?>>Minivan</option>
                    <option value="Pickup" <?= ($tipo == 'Pickup') ? 'selected' : '' ?>>Pickup</option>
                </select>
            </div>

            <div class="col-md-3">
                <label for="transmision" class="form-label">Transmisión</label>
                <select class="form-select" id="transmision" name="transmision">
                    <option value="">Selecciona una transmisión</option>
                    <option value="Standard" <?= ($transmision == 'Standard') ? 'selected' : '' ?>>Standard</option>
                    <option value="Automatica" <?= ($transmision == 'Automatica') ? 'selected' : '' ?>>Automática</option>
                </select>
            </div>

            <div class="col-md-12 text-center">
                <button type="submit" class="btn btn-custom">Buscar</button>
            </div>
        </form>
    </div>

    <!-- Car Cards -->
    <div class="container mt-5">
    <div class="row" id="car-list">
        <?php foreach ($cars as $car): ?>
        <div class="col-md-4">
            <div class="card car-card shadow-box position-relative">
                <a href="<?php echo $car['catWhatsapp'] ?>" class="stretched-link"></a>
                <img src="<?= $car['imagen_url'] ?>" class="car-img" alt="<?= $car['modelo'] ?>">
                <div class="car-info">
                    <h5><?= $car['marca'] . ' ' . $car['modelo'] ?></h5>
                    <p class="car-price">$<?= number_format($car['precio'], 2) ?></p>
                    <div class="car-details">
                        <span>Marca: <?= $car['marca'] ?></span>
                        <span>Millas: <?= number_format($car['kilometraje']) ?> mi</span>
                        <br>
                        <span>Tipo: <?= $car['tipo'] ?></span>
                        <span>Transmisión: <?= $car['transmision'] ?></span>
                        <br>
                        <span>Estado: <?= $car['estado'] ?></span>
                    </div>
                </div>
            </div>
        </div>

            <?php endforeach; ?>
        </div>
    </div>
  <!-- Ubicación Section -->
<!-- Ubicación -->
<div class="container mt-5">
    <div class="card shadow-lg border-0">
        <div class="card-body text-center">
            <h3 class="card-title">Ubicación</h3>
            <p class="small text-muted">📍 Boulevard Constitución, 200 metros antes de Ex Alba Constitución, carretera principal, Bodegas Integración, San Salvador.</p>
            <div class="mt-3">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3875.5004525715894!2d-89.2209082249098!3d13.74866668664289!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zMTPCsDQ0JzU1LjIiTiA4OcKwMTMnMDYuMCJX!5e0!3m2!1sit!2sgt!4v1740545506838!5m2!1sit!2sgt" 
                    allowfullscreen 
                    style="width: 100%; height: 300px; border-radius: 10px; border: none;"
                ></iframe>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
        <div class="card shadow-lg border-0">
            <div class="row align-items-center g-0">
                <div class="col-md-4">
                    <center>
                    <img src="rivas400.jpeg" alt="Nuestra Historia" class="img-fluid history-img">
                    </center>
                </div>
                <div class="col-md-8 p-4">
                    <h2 class="card-title">Nuestra Historia</h2>
                    <p class="card-text">
                    RMotors Imports es una empresa familiar fundada en 2019 por la familia Rivas. Desde sus inicios, hemos trabajado arduamente para construir una sólida cartera de clientes basada en confianza y prestigio. Nos especializamos en la importación de vehículos con mínimos daños, asegurando calidad y seguridad para nuestros compradores.
                    <br>
                    Nuestro Gerente General, Joaquín Rivas , cuenta con más de 11 años de experiencia en el sector automotriz. Con tan solo 29 años, ha liderado un equipo comprometido con la excelencia y la transparencia, consolidando a RMotors Imports como una de las empresas más respetadas del rubro en El Salvador.

                    </p>
                </div>
            </div>
        </div>
    </div>

<!-- Redes Sociales -->
<div class="container mt-5">
    <div class="card shadow-lg border-0" style="background: linear-gradient(135deg, #f8f9fa, #e9ecef);">
        <div class="card-body text-center p-4">
            <h3 class="card-title fw-bold text-primary">¿Tienes consultas?</h3>
            <p class="small text-muted mb-3">¡Contáctanos ya!</p>
            <div class="d-flex flex-column align-items-center">
                <a href="tel:+50379374068" class="btn btn-outline-primary w-75 my-1">
                    <i class="fas fa-phone"></i> +503 7937 4068
                </a>
                <a href="tel:+50377409363" class="btn btn-outline-success w-75 my-1">
                    <i class="fas fa-phone"></i> +503 7740 9363
                </a>
            </div>
        </div>
    </div>
</div>


<!-- Misión y Visión (50-50 line) -->
<div class="container mt-5">
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-lg border-0">
                <div class="card-body">
                    <h2 class="card-title">Misión</h2>
                    <p class="card-text">Importar la mejor de calidad de autos al mercado salvadoreño, vehículos con pocos daños, en condiciones de funcionamiento  para la seguridad del país, asimismo mantener precios competitivos, dar un servicio de calidad con profesionalismo, honestidad y compromiso.</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-lg border-0">
                <div class="card-body">
                    <h2 class="card-title">Visión</h2>
                    <p class="card-text">Consolidarnos como una empresa líder en el mercado de automóviles salvadoreño, que nuestros clientes puedan crecer con nosotros y ahorrar a la hora de adquirir nuestras unidades, posicionarnos como un referente de responsabilidad con el cliente y poder llegar siempre a todo el mercado salvadoreño en sus 14 departamentos como actualmente lo estamos haciendo.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<br>
    <!-- Footer -->
    <footer>
        <div class="footer-links">
            <a href="#">Inicio</a>
            <a href="#">Nosotros</a>
            <a href="#">Contáctanos</a>
        </div>
    </footer>

    <!-- Bootstrap JS and Popper -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>


