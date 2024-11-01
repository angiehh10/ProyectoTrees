<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Amigo') {
    header("Location: index.php");
    exit;
}
?>

<?php include 'inc/header.php'; ?>

<div class="container mt-5">
    <h1>Bienvenido Amigo</h1>
    <p>Esta es la página de amigo donde solo los usuarios con permisos de amigo pueden acceder.</p>
    <!-- Aquí puedes agregar contenido específico para el amigo -->
</div>

<?php include 'inc/footer.php'; ?>