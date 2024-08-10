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

    public function iniciarSesion($correo, $psw) {
        $stmt = $this->db->conn->prepare("SELECT * FROM usuarios WHERE correo = ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $usuario = $result->fetch_assoc();
            // Verificar la contraseña (asegúrate de usar password_hash y password_verify en producción)
            if ($usuario['psw'] === $psw) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['nombre'] = $usuario['nombre'];
                $_SESSION['rol'] = $usuario['rol']; // Almacenar el rol del usuario

                // Redirigir según el rol
                if ($usuario['rol'] === 'usuario') {
                    header("Location: index.html"); // Redirigir a index.html
                } else {
                    header("Location: admin.php"); // Redirigir a admin.php
                }
                exit();
            } else {
                return "Contraseña incorrecta.";
            }
        } else {
            return "No existe un usuario con ese correo.";
        }
    }
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST['correo'];
    $psw = $_POST['psw'];

    $usuario = new Usuario();
    $resultado = $usuario->iniciarSesion($correo, $psw);

    if ($resultado !== true) {
        $error = $resultado;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f0f0;
        }
        .login-container {
            max-width: 400px;
            margin: auto;
            margin-top: 100px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2 class="text-center">Iniciar Sesión</h2>
        <?php if ($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <form method="post" action="">
            <div class="form-group">
                <label for="correo">Correo:</label>
                <input type="email" class="form-control" name="correo" required>
            </div>
            <div class="form-group">
                <label for="psw">Contraseña:</label>
                <input type="password" class="form-control" name="psw" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Iniciar Sesión</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
