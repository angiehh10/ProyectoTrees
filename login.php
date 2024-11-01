<?php
include 'inc/header.php';
include 'utils/functions.php';

$loginError = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $contrasena = $_POST['contrasena'];

    // Llamar a la función loginUser
    $result = loginUser($email, $contrasena);

    if ($result['status'] === 'success') {
        // Redirigir a la página correspondiente según el rol
        header("Location: " . $result['redirect']);
        exit;
    } else {
        // Mostrar mensaje de error si ocurre un problema
        $loginError = $result['message'];
    }
}
?>

<!-- Formulario de inicio de sesión -->
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white text-center">
                    <h5>Iniciar Sesión</h5>
                </div>
                <div class="card-body">
                    <?php if ($loginError): ?>
                        <div class="alert alert-danger text-center"><?php echo $loginError; ?></div>
                    <?php endif; ?>
                    <form action="login.php" method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" name="email" placeholder="Ingresa tu correo electrónico" required>
                        </div>
                        <div class="mb-3">
                            <label for="contrasena" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" name="contrasena" placeholder="Ingresa tu contraseña" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Iniciar Sesión</button>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <p class="mb-0">¿No tienes cuenta? <a href="signup.php" class="text-success">Regístrate</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'inc/footer.php'; ?>

