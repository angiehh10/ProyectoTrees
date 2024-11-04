<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Amigo') {
    header("Location: index.php");
    exit;
}
include 'utils/functions.php';
$usuario_id = $_SESSION['usuario_id'];
$arbolesAmigo = obtenerArbolesPorAmigo($usuario_id);
$arbolesDisponibles = obtenerArbolesDisponibles();
?>

<?php include 'inc/header.php'; ?>

<div class="container mt-5">
    <h1>Bienvenido Amigo</h1>
    <p>Esta es la página de amigo donde solo los usuarios con permisos de amigo pueden acceder.</p>
    
    <?php if (isset($_GET['compra']) && $_GET['compra'] === 'exito'): ?>
        <div class="alert alert-success">Compra realizada con éxito.</div>
    <?php endif; ?>

    <h2>Mis Árboles</h2>
    <?php if (!empty($arbolesAmigo)): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Especie</th>
                    <th>Ubicación</th>
                    <th>Precio</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($arbolesAmigo as $arbol): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($arbol['especie']); ?></td>
                        <td><?php echo htmlspecialchars($arbol['ubicacion_geografica']); ?></td>
                        <td><?php echo htmlspecialchars($arbol['precio']); ?></td>
                        <td>
                            <a href="detalles.php?id=<?php echo $arbol['id']; ?>">Ver Detalles</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No tienes árboles registrados.</p>
    <?php endif; ?>

    <h2>Árboles Disponibles</h2>
    <?php if (!empty($arbolesDisponibles)): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Especie</th>
                    <th>Ubicación</th>
                    <th>Precio</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($arbolesDisponibles as $arbol): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($arbol['especie']); ?></td>
                        <td><?php echo htmlspecialchars($arbol['ubicacion_geografica']); ?></td>
                        <td><?php echo htmlspecialchars($arbol['precio']); ?></td>
                        <td>
                            <a href="compra.php?id=<?php echo $arbol['id']; ?>">Solicitar Compra</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay árboles disponibles.</p>
    <?php endif; ?>
</div>

<?php include 'inc/footer.php'; ?>
