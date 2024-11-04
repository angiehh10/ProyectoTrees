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

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Procesar la compra
        $usuario_id = $_SESSION['usuario_id'];

        // Registrar la relación entre el amigo y el árbol
        if (registrarCompra($usuario_id, $arbol_id)) { 
            // Actualizar el estado del árbol a "Vendido"
            actualizarEstadoArbol($arbol_id, 'Vendido'); 
            // Redirigir a amigo.php con un mensaje de éxito
            header("Location: amigo.php?compra=exito");
            exit;
        } else {
            echo "<div class='alert alert-danger'>Error al registrar la compra.</div>";
        }
    }
} else {
    echo "<div class='alert alert-danger'>Árbol no válido.</div>";
    exit;
}

include 'inc/header.php';
?>

<div class="container mt-5">
    <h1>Comprar Árbol</h1>
    
    <?php if ($arbol): ?>
        <h2><?php echo htmlspecialchars($arbol['nombre_comercial']); ?></h2>
        <p>Ubicación: <?php echo htmlspecialchars($arbol['ubicacion_geografica']); ?></p>
        <p>Precio: <?php echo htmlspecialchars($arbol['precio']); ?></p>
        
        <?php if (!empty($arbol['foto'])): ?>
            <img src="<?php echo htmlspecialchars($arbol['foto']); ?>" alt="<?php echo htmlspecialchars($arbol['nombre_comercial']); ?>" style="max-width: 300px; height: auto;">
        <?php endif; ?>
        
        <form method="post" action="">
            <button type="submit" class="btn btn-success">Confirmar Compra</button>
            <a href="amigo.php" class="btn btn-danger">Cancelar Compra</a>
        </form>
        
    <?php else: ?>
        <p>No se encontró información del árbol.</p>
    <?php endif; ?>
</div>

<?php include 'inc/footer.php'; ?>
