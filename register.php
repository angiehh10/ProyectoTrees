<?php
include 'inc/header.php';
include 'utils/functions.php';

$registerError = null;
$registerSuccess = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_usuario = $_POST['nombre_usuario'];
    $email = $_POST['email'];
    $contrasena = $_POST['contrasena'];
    $rol = 'Amigo'; // Asumimos que solo los amigos pueden registrarse desde este formulario
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];
    $pais = $_POST['pais'];

    $result = registerUser($nombre_usuario, $email, $contrasena, $rol, 'Activo', $nombre, $apellidos, $telefono, $direccion, $pais);

    if ($result === true) {
        $registerSuccess = "Registro exitoso. ¡Ahora puedes iniciar sesión!";
    } else {
        $registerError = $result;
    }
}
?>

<!-- Formulario de registro con todos los campos obligatorios -->
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white text-center">
                    <h5>Regístrate en MyTrees</h5>
                </div>
                <div class="card-body">
                    <?php if ($registerError): ?>
                        <div class="alert alert-danger text-center"><?php echo $registerError; ?></div>
                    <?php elseif ($registerSuccess): ?>
                        <div class="alert alert-success text-center"><?php echo $registerSuccess; ?></div>
                    <?php endif; ?>
                    <form action="register.php" method="POST">
                        <div class="mb-3">
                            <label for="nombre_usuario" class="form-label">Nombre de Usuario</label>
                            <input type="text" class="form-control" name="nombre_usuario" placeholder="Ingresa tu nombre de usuario" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" name="email" placeholder="Ingresa tu correo electrónico" required>
                        </div>
                        <div class="mb-3">
                            <label for="contrasena" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" name="contrasena" placeholder="Ingresa tu contraseña" required>
                        </div>
                        <!-- Campos adicionales para los amigos, todos requeridos -->
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" name="nombre" placeholder="Ingresa tu nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="apellidos" class="form-label">Apellidos</label>
                            <input type="text" class="form-control" name="apellidos" placeholder="Ingresa tus apellidos" required>
                        </div>
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Número de Teléfono</label>
                            <input type="text" class="form-control" name="telefono" placeholder="Ingresa tu número de teléfono" required>
                        </div>
                        <div class="mb-3">
                            <label for="direccion" class="form-label">Dirección</label>
                            <input type="text" class="form-control" name="direccion" placeholder="Ingresa tu dirección" required>
                        </div>
                        <div class="mb-3">
                            <label for="pais" class="form-label">País</label>
                            <input type="text" class="form-control" name="pais" placeholder="Ingresa tu país" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Registrar</button>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <p class="mb-0">¿Ya tienes una cuenta? <a href="login.php" class="text-primary">Inicia Sesión</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'inc/footer.php'; ?>
