<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Amigo') {
    header("Location: index.php");
    exit;
}

include 'utils/functions.php';

// Verificar si se ha enviado el ID del árbol
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $arbol_id = intval($_GET['id']);
    
    // Obtener detalles del árbol
    $arbol = obtenerArbolPorId($arbol_id);

    // Verificar si el árbol pertenece al usuario Amigo
    $usuario_id = $_SESSION['usuario_id'];
    $perteneceAlUsuario = perteneceAlUsuario($usuario_id, $arbol_id);

    if (!$perteneceAlUsuario) {
        echo "<div class='alert alert-danger'>No tienes permiso para ver los detalles de este árbol.</div>";
        exit;
    }

} else {
    echo "<div class='alert alert-danger'>Árbol no válido.</div>";
    exit;
}

include 'inc/header.php';
?>

<div class="container mt-5">
    <h1>Detalles del Árbol</h1>
    
    <?php if ($arbol): ?>
        <h2><?php echo htmlspecialchars($arbol['nombre_comercial']); ?></h2>
        <p>Ubicación: <?php echo htmlspecialchars($arbol['ubicacion_geografica']); ?></p>
        <p>Precio: <?php echo htmlspecialchars($arbol['precio']); ?></p>
        <p>Estado: <?php echo htmlspecialchars($arbol['estado']); ?></p>
        
        <?php if (!empty($arbol['foto'])): ?>
            <img src="<?php echo htmlspecialchars($arbol['foto']); ?>" alt="<?php echo htmlspecialchars($arbol['nombre_comercial']); ?>" style="max-width: 300px; height: auto;">
        <?php endif; ?>
        
        <a href="amigo.php" class="btn btn-primary">Volver</a>
        
    <?php else: ?>
        <p>No se encontró información del árbol.</p>
    <?php endif; ?>
</div>

<?php include 'inc/footer.php'; ?>
