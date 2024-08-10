<?php
session_start();

class Database {
    private $servername = "localhost"; // Cambia esto según tu configuración
    private $username = "root"; // Cambia esto según tu configuración
    private $password = ""; // Cambia esto según tu configuración
    private $dbname = "tienda";
    public $conn;

    public function __construct() {
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
        if ($this->conn->connect_error) {
            die("Conexión fallida: " . $this->conn->connect_error);
        }
    }
}

class Usuario {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function registrar($nombre, $correo, $psw, $rol) {
        // Comprobar si el correo ya existe
        $stmt = $this->db->conn->prepare("SELECT * FROM usuarios WHERE correo = ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return "El correo ya está registrado.";
        }

        // Insertar el nuevo usuario sin encriptar la contraseña
        $stmt = $this->db->conn->prepare("INSERT INTO usuarios (nombre, correo, psw, rol) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nombre, $correo, $psw, $rol);
        
        if ($stmt->execute()) {
            return true; // Registro exitoso
        } else {
            return "Error al registrar: " . $stmt->error;
        }
    }
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $psw = $_POST['psw'];
    $rol = $_POST['rol'];

    $usuario = new Usuario();
    $resultado = $usuario->registrar($nombre, $correo, $psw, $rol);

    if ($resultado === true) {
        header("Location: login.php"); // Redirigir a la página de inicio de sesión
        exit();
    } else {
        $error = $resultado;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>CROQ SHOP - Registro</title>
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" />
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
                </ul>
            </div>
        </div>
    </nav>

    <!-- Header -->
    <header class="bg-primary py-5">
        <div class="container px-4 px-lg-5 my-5">
            <div class="text-center text-white">
                <h1 class="display-4 fw-bolder">Registro de Usuarios</h1>
            </div>
        </div>
    </header>

    <!-- Registro Section -->
    <section class="py-5">
        <div class="container px-4 px-lg-5">
            <div class="row">
                <center>
                    <div class="col-md-6">
                        <h2 class="text-center mb-4">Registro</h2>
                        <?php if ($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
                        <form action="" method="post" class="p-4 bg-light rounded shadow">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre:</label>
                                <input type="text" class="form-control" name="nombre" id="nombre" required>
                            </div>
                            <div class="mb-3">
                                <label for="correo" class="form-label">Correo Electrónico:</label>
                                <input type="email" class="form-control" name="correo" required>
                            </div>
                            <div class="mb-3">
                                <label for="psw" class="form-label">Contraseña:</label>
                                <input type="password" class="form-control" name="psw" required>
                            </div>
                            <div class="mb-3">
                                <label for="rol" class="form-label">Rol:</label>
                                <select class="form-select" name="rol" required>
                                    <option value="usuario">Usuario</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Registrar</button>
                        </form>
                    </div>
                </center>
            </div>
        </div>
    </section>
    
    <!-- Bootstrap core JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
