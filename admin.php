<?php
class Database {
    private $conexion;

    public function __construct($host, $usuario, $password, $nombreBD) {
        $this->conexion = new mysqli($host, $usuario, $password, $nombreBD);

        if ($this->conexion->connect_error) {
            die("Error de conexión: " . $this->conexion->connect_error);
        }
    }

    public function getConexion() {
        return $this->conexion;
    }
}

class Producto {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function crear($nombre, $precio, $imagen) {
        $stmt = $this->conexion->prepare("INSERT INTO productos (nombre, precio, imagen) VALUES (?, ?, ?)");
        $stmt->bind_param("sds", $nombre, $precio, $imagen);
        if (!$stmt->execute()) {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }

    public function actualizar($id, $nombre, $precio) {
        $stmt = $this->conexion->prepare("UPDATE productos SET nombre = ?, precio = ? WHERE id = ?");
        $stmt->bind_param("sdi", $nombre, $precio, $id);
        if (!$stmt->execute()) {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }

    public function eliminar($id) {
        $stmt = $this->conexion->prepare("DELETE FROM productos WHERE id = ?");
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }

    public function obtenerTodos() {
        return $this->conexion->query("SELECT * FROM productos");
    }
}

// Conexión a la base de datos
$db = new Database("localhost", "root", "", "tienda");
$conexion = $db->getConexion();
$producto = new Producto($conexion);

// Manejo de las operaciones CRUD
if (isset($_POST['accion'])) {
    $accion = $_POST['accion'];
    
    if ($accion == 'crear') {
        $nombre = $_POST['nombre'];
        $precio = $_POST['precio'];
        $imagen = file_get_contents($_FILES['imagen']['tmp_name']);
        $producto->crear($nombre, $precio, $imagen);
        
    } elseif ($accion == 'actualizar') {
        $id = $_POST['id'];
        $nombre = $_POST['nombre'];
        $precio = $_POST['precio'];
        $producto->actualizar($id, $nombre, $precio);
        
    } elseif ($accion == 'eliminar') {
        $id = $_POST['id'];
        $producto->eliminar($id);
    }
}

$resultado = $producto->obtenerTodos();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>CROQ SHOP</title>
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.min.css">

</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-success">
        <div class="container px-4 px-lg-5">
            <a class="navbar-brand text-white" href="#!">CROQ SHOP</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                    <li class="nav-item"><a class="nav-link active text-white" aria-current="page" href="#!">Home</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="#!">About</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Shop</a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="#!">All Products</a></li>
                            <li><hr class="dropdown-divider" /></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Header -->
    <header class="bg-primary py-5">
        <div class="container px-4 px-lg-5 my-5">
            <div class="text-center text-white">
                <h1 class="display-4 fw-bolder">
                    <span class="animated-text" id="text1">Bienvenidos</span> 
                    <span class="animated-text" id="text2">A CROP</span>
                    <span class="animated-text" id="text3">SHOP</span>
                </h1>
                <p class="lead fw-normal text-white-50 mb-0">Actualizar Productos</p>
            </div>
        </div>
    </header>

    <!-- CRUD Section -->
    <section class="py-5">
        <div class="container px-4 px-lg-5">
            <div class="row">
                <center>
                    <div class="col-md-6">
                        <h2 class="text-center mb-4">Agregar Producto</h2>
                        <form action="admin.php" method="post" enctype="multipart/form-data" class="p-4 bg-light rounded shadow">
                            <input type="hidden" name="accion" value="crear">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre:</label>
                                <input type="text" class="form-control" name="nombre" id="nombre" required>
                            </div>
                            <div class="mb-3">
                                <label for="precio" class="form-label">Precio:</label>
                                <input type="number" class="form-control" step="0.01" name="precio" id="precio" required>
                            </div>
                            <div class="mb-3">
                                <label for="imagen" class="form-label">Imagen:</label>
                                <input type="file" class="form-control" name="imagen" id="imagen" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Agregar Producto</button>
                        </form>
                    </div>
                </center>
            </div>
            <div class="row mt-4">
                <div class="col-md-12">
                    <h2>Lista de Productos</h2>
                    <div class="row">
                        <?php
                        if ($resultado->num_rows > 0) {
                            while ($row = $resultado->fetch_assoc()) {
                                echo '<div class="col-6 col-md-4 mb-4">';
                                echo '<div class="card">';
                                echo '<img src="data:image/jpeg;base64,' . base64_encode($row['imagen']) . '" class="card-img-top" alt="' . $row['nombre'] . '">';
                                echo '<div class="card-body">';
                                echo '<h5 class="card-title">' . $row['nombre'] . '</h5>';
                                echo '<p class="card-text">$' . number_format($row['precio'], 2) . '</p>';
                                echo '<div class="d-flex justify-content-between">';
                                echo '<button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalActualizar" data-id="' . $row['id'] . '" data-nombre="' . $row['nombre'] . '" data-precio="' . $row['precio'] . '">Actualizar</button>';
                                echo '<form action="admin.php" method="post" style="display:inline;">';
                                echo '<input type="hidden" name="accion" value="eliminar">';
                                echo '<input type="hidden" name="id" value="' . $row['id'] . '">';
                                echo '<button type="submit" class="btn btn-danger btn-sm">Borrar</button>';
                                echo '</form>';
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                            }
                        } else {
                            echo '<p>No hay productos disponibles.</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal para actualizar producto -->
    <div class="modal fade" id="modalActualizar" tabindex="-1" aria-labelledby="modalActualizarLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalActualizarLabel">Actualizar Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="admin.php" method="post" id="formActualizar">
                        <input type="hidden" name="accion" value="actualizar">
                        <input type="hidden" name="id" id="modalId" value="">
                        <div class="mb-3">
                            <label for="modalNombre" class="form-label">Nombre:</label>
                            <input type="text" class="form-control" name="nombre" id="modalNombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="modalPrecio" class="form-label">Precio:</label>
                            <input type="number" class="form-control" step="0.01" name="precio" id="modalPrecio" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Actualizar Producto</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script para llenar el modal con los datos del producto
        const modalActualizar = document.getElementById('modalActualizar');
        modalActualizar.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const nombre = button.getAttribute('data-nombre');
            const precio = button.getAttribute('data-precio');

            const modalId = modalActualizar.querySelector('#modalId');
            const modalNombre = modalActualizar.querySelector('#modalNombre');
            const modalPrecio = modalActualizar.querySelector('#modalPrecio');

            modalId.value = id;
            modalNombre.value = nombre;
            modalPrecio.value = precio;
        });
    </script>
</body>
</html>
