<?php
session_start();

class Database {
    private $servername = "localhost"; 
    private $username = "root"; 
    private $password = ""; 
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

    public function obtenerUsuarios() {
        $stmt = $this->db->conn->prepare("SELECT * FROM usuarios");
        $stmt->execute();
        return $stmt->get_result();
    }

    public function editarUsuario($id, $nombre, $correo, $psw, $rol) {
        $stmt = $this->db->conn->prepare("UPDATE usuarios SET nombre = ?, correo = ?, psw = ?, rol = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $nombre, $correo, $psw, $rol, $id);
        return $stmt->execute();
    }

    public function eliminarUsuario($id) {
        $stmt = $this->db->conn->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}

$usuario = new Usuario();
$resultadoUsuarios = $usuario->obtenerUsuarios();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['accion']) && $_POST['accion'] === 'editar') {
        $id = $_POST['id'];
        $nombre = $_POST['nombre'];
        $correo = $_POST['correo'];
        $psw = $_POST['psw'];
        $rol = $_POST['rol'];
        $usuario->editarUsuario($id, $nombre, $correo, $psw, $rol);
    }

    if (isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
        $id = $_POST['id'];
        $usuario->eliminarUsuario($id);
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
    <title>CROQ SHOP - Usuarios</title>
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
                    <li class="nav-item"><a class="nav-link active text-white" aria-current="page" href="admin.php">Productos</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="usuarios.php">Usuarios</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Header -->
    <header class="bg-primary py-5">
        <div class="container px-4 px-lg-5 my-5">
            <div class="text-center text-white">
                <h1 class="display-4 fw-bolder">Lista De Usuarios</h1>
            </div>
        </div>
    </header>

    <!-- Tabla de Usuarios -->
    <section class="py-5">
        <div class="container px-4 px-lg-5">
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <th>Rol</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($usuario = $resultadoUsuarios->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $usuario['id']; ?></td>
                                    <td><?php echo $usuario['nombre']; ?></td>
                                    <td><?php echo $usuario['correo']; ?></td>
                                    <td><?php echo $usuario['rol']; ?></td>
                                    <td>
                                        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editarModal<?php echo $usuario['id']; ?>">Editar</button>
                                        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#eliminarModal<?php echo $usuario['id']; ?>">Eliminar</button>
                                    </td>
                                </tr>

                                <!-- Modal Editar -->
                                <div class="modal fade" id="editarModal<?php echo $usuario['id']; ?>" tabindex="-1" aria-labelledby="editarModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editarModalLabel">Editar Usuario</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post">
                                                    <input type="hidden" name="accion" value="editar">
                                                    <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                                                    <div class="mb-3">
                                                        <label for="nombre" class="form-label">Nombre:</label>
                                                        <input type="text" class="form-control" name="nombre" value="<?php echo $usuario['nombre']; ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="correo" class="form-label">Correo:</label>
                                                        <input type="email" class="form-control" name="correo" value="<?php echo $usuario['correo']; ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="psw" class="form-label">Contraseña:</label>
                                                        <input type="text" class="form-control" name="psw" value="<?php echo $usuario['psw']; ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="rol" class="form-label">Rol:</label>
                                                        <select class="form-select" name="rol" required>
                                                            <option value="usuario" <?php echo $usuario['rol'] === 'usuario' ? 'selected' : ''; ?>>Usuario</option>
                                                            <option value="admin" <?php echo $usuario['rol'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                                        </select>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Eliminar -->
                                <div class="modal fade" id="eliminarModal<?php echo $usuario['id']; ?>" tabindex="-1" aria-labelledby="eliminarModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="eliminarModalLabel">Eliminar Usuario</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                ¿Estás seguro de que deseas eliminar a este usuario?
                                            </div>
                                            <div class="modal-footer">
                                                <form method="post">
                                                    <input type="hidden" name="accion" value="eliminar">
                                                    <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-danger">Eliminar</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Bootstrap core JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
