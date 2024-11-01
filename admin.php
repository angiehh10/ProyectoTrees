<?php
include 'utils/functions.php';

// Obtener estadísticas
$totalAmigos = countAmigos();
$totalArbolesDisponibles = countArbolesDisponibles();
$totalArbolesVendidos = countArbolesVendidos();

// Variables para mensajes
$mensaje = '';
$error = '';

// Lógica para manejar formularios y acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Acción para especies
    if (isset($_POST['accion_especie'])) {
        if ($_POST['accion_especie'] === 'crear') {
            $nombre_comercial = $_POST['nombre_comercial'];
            $nombre_cientifico = $_POST['nombre_cientifico'];
            if (crearEspecie($nombre_comercial, $nombre_cientifico)) {
                $mensaje = "Especie creada exitosamente.";
            } else {
                $error = "Error al crear la especie.";
            }
        } elseif ($_POST['accion_especie'] === 'actualizar') {
            $id = $_POST['especie_id'];
            $nombre_comercial = $_POST['nombre_comercial'];
            $nombre_cientifico = $_POST['nombre_cientifico'];
            if (actualizarEspecie($id, $nombre_comercial, $nombre_cientifico)) {
                $mensaje = "Especie actualizada exitosamente.";
            } else {
                $error = "Error al actualizar la especie.";
            }
        } elseif ($_POST['accion_especie'] === 'eliminar') {
            $id = $_POST['especie_id'];
            if (eliminarEspecie($id)) {
                $mensaje = "Especie eliminada exitosamente.";
            } else {
                $error = "Error al eliminar la especie.";
            }
        }
    }
    // Acción para árboles
    elseif (isset($_POST['accion_arbol']) && $_POST['accion_arbol'] === 'crear') {
        $especie_id = $_POST['especie_id'];
        $ubicacion_geografica = $_POST['ubicacion_geografica'];
        $estado = $_POST['estado'];
        $precio = $_POST['precio'];
        $tamano = $_POST['tamano'];

        // Manejo de la imagen: URL o archivo subido
        $foto = null;

        if (!empty($_POST['foto_url'])) {
            // Si se proporciona una URL, usar esa URL para la foto
            $foto = $_POST['foto_url'];
        } elseif (isset($_FILES['foto_upload']) && $_FILES['foto_upload']['error'] === UPLOAD_ERR_OK) {
            // Si se sube un archivo, manejar la carga y almacenar la ruta del archivo
            $nombreArchivo = basename($_FILES['foto_upload']['name']);
            $directorioDestino = 'uploads/' . $nombreArchivo;

            // Crear el directorio si no existe
            if (!file_exists('uploads')) {
                mkdir('uploads', 0777, true);
            }

            // Mover el archivo subido a la carpeta de destino
            if (move_uploaded_file($_FILES['foto_upload']['tmp_name'], $directorioDestino)) {
                $foto = $directorioDestino; // Guarda la ruta del archivo en la base de datos
            } else {
                $error = "Error al cargar la imagen.";
            }
        }

        // Crear el árbol con la información procesada
        if (crearArbol($especie_id, $ubicacion_geografica, $estado, $precio, $tamano, $foto)) {
            $mensaje = "Árbol creado exitosamente.";
        } else {
            $error = "Error al crear el árbol.";
        }
    }
    // Acción para registrar actualización de árbol
    elseif (isset($_POST['accion_actualizacion'])) {
        $arbol_id = $_POST['arbol_id'];
        $tamano = $_POST['tamano'];
        $estado = $_POST['estado'];
        if (registrarActualizacion($arbol_id, $tamano, $estado)) {
            $mensaje = "Actualización registrada exitosamente.";
        } else {
            $error = "Error al registrar la actualización.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - MyTrees</title>
    
    <!-- Vincula el CSS de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/estilos.css" rel="stylesheet">
</head>
<body>
    <?php include 'inc/header.php'; ?>

    <div class="container my-5">
    <h1 class="text-center mb-4">Dashboard de Administración</h1>
    <div class="row text-center">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Amigos Registrados</h5>
                    <p class="card-text display-4"><?php echo $totalAmigos; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Árboles Disponibles</h5>
                    <p class="card-text display-4"><?php echo $totalArbolesDisponibles; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Árboles Vendidos</h5>
                    <p class="card-text display-4"><?php echo $totalArbolesVendidos; ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Mostrar mensajes de éxito o error -->
    <?php if ($mensaje): ?>
        <div class="alert alert-success text-center"><?php echo $mensaje; ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger text-center"><?php echo $error; ?></div>
    <?php endif; ?>

    <!-- Menú de Pestañas -->
    <ul class="nav nav-tabs" id="adminTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link <?php echo (!isset($_GET['tab']) || $_GET['tab'] == 'especies') ? 'active' : ''; ?>" href="admin.php?tab=especies">Administrar Especies</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'arboles') ? 'active' : ''; ?>" href="admin.php?tab=arboles">Administrar Árboles</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'actualizacion') ? 'active' : ''; ?>" href="admin.php?tab=actualizacion">Registrar Actualización</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'amigos') ? 'active' : ''; ?>" href="admin.php?tab=amigos">Ver Amigos</a>
        </li>
    </ul>

    <!-- Contenido de Pestañas -->
    <div class="tab-content" id="adminTabContent">
        <!-- Pestaña Administrar Especies -->
        <div class="tab-pane fade <?php echo (!isset($_GET['tab']) || $_GET['tab'] == 'especies') ? 'show active' : ''; ?>" id="especies">
            <div class="card my-4">
                <div class="card-body">
                    <h4>Crear Nueva Especie</h4>
                    <form action="admin.php" method="POST">
                        <input type="hidden" name="accion_especie" value="crear">
                        <div class="mb-3">
                            <label for="nombre_comercial" class="form-label">Nombre Comercial</label>
                            <input type="text" name="nombre_comercial" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="nombre_cientifico" class="form-label">Nombre Científico</label>
                            <input type="text" name="nombre_cientifico" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success">Crear Especie</button>
                    </form>

                    <h5 class="mt-4">Especies Existentes</h5>
                    <?php
                    $especies = obtenerEspecies();
                    foreach ($especies as $especie): ?>
                        <form action="admin.php" method="POST" class="d-flex justify-content-between align-items-center mt-2">
                            <input type="hidden" name="accion_especie" value="actualizar">
                            <input type="hidden" name="especie_id" value="<?php echo $especie['id']; ?>">
                            <input type="text" name="nombre_comercial" value="<?php echo $especie['nombre_comercial']; ?>" class="form-control me-2">
                            <input type="text" name="nombre_cientifico" value="<?php echo $especie['nombre_cientifico']; ?>" class="form-control me-2">
                            <button type="submit" class="btn btn-primary">Actualizar</button>
                            <button type="submit" formaction="admin.php" name="accion_especie" value="eliminar" class="btn btn-danger">Eliminar</button>
                        </form>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
       <!-- Pestaña Administrar Árboles -->
<div class="tab-pane fade <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'arboles') ? 'show active' : ''; ?>" id="arboles">
    <div class="card my-4">
        <div class="card-body">
            <form action="admin.php?tab=arboles" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="accion_arbol" value="crear">
                <div class="mb-3">
                    <label for="especie_id" class="form-label">Especie</label>
                    <select name="especie_id" class="form-control" required>
                        <?php foreach ($especies as $especie): ?>
                            <option value="<?php echo $especie['id']; ?>"><?php echo $especie['nombre_comercial']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="ubicacion_geografica" class="form-label">Ubicación Geográfica</label>
                    <input type="text" name="ubicacion_geografica" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="estado" class="form-label">Estado</label>
                    <select name="estado" class="form-control" required>
                        <option value="Disponible">Disponible</option>
                        <option value="Vendido">Vendido</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="precio" class="form-label">Precio</label>
                    <input type="number" step="0.01" name="precio" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="foto" class="form-label">Foto del Árbol</label>
                    <input type="url" name="foto_url" class="form-control" placeholder="URL de la foto del árbol">
                    <input type="file" name="foto_upload" class="form-control mt-2" accept="image/*">
                </div>
                <div class="mb-3">
                    <label for="tamano" class="form-label">Tamaño</label>
                    <input type="text" name="tamano" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success">Crear Árbol</button>
            </form>
        </div>
    </div>
</div>
        <!-- Pestaña Registrar Actualización de Árbol -->
        <div class="tab-pane fade <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'actualizacion') ? 'show active' : ''; ?>" id="actualizacion">
            <div class="card my-4">
                <div class="card-body">
                    <form action="admin.php" method="POST">
                        <input type="hidden" name="accion_actualizacion" value="registrar">
                        <div class="mb-3">
                            <label for="arbol_id" class="form-label">ID del Árbol</label>
                            <input type="number" name="arbol_id" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="tamano" class="form-label">Tamaño Actual</label>
                            <input type="text" name="tamano" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="estado" class="form-label">Estado Actual</label>
                            <select name="estado" class="form-control" required>
                                <option value="Disponible">Disponible</option>
                                <option value="Vendido">Vendido</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">Registrar Actualización</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Pestaña Ver Amigos -->
        <div class="tab-pane fade <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'amigos') ? 'show active' : ''; ?>" id="amigos">
            <div class="card my-4">
                <div class="card-body">
                    <h5 class="mt-4">Lista de Amigos</h5>
                    <?php
                    $amigos = obtenerAmigos();
                    foreach ($amigos as $amigo): ?>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span><?php echo $amigo['nombre']; ?></span>
                            <a href="admin.php?tab=amigos&amigo_id=<?php echo $amigo['id']; ?>" class="btn btn-info">Ver Árboles</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Árboles del Amigo Seleccionado -->
            <?php if (isset($_GET['amigo_id'])): ?>
                <div class="card my-4">
                    <div class="card-body">
                        <h5 class="mt-4">Árboles de <?php echo obtenerNombreAmigo($_GET['amigo_id']); ?></h5>
                        <?php
                        $arboles = obtenerArbolesPorAmigo($_GET['amigo_id']);
                        foreach ($arboles as $arbol): ?>
                            <form action="admin.php?tab=amigos&amigo_id=<?php echo $_GET['amigo_id']; ?>" method="POST" class="d-flex justify-content-between align-items-center mt-2">
                                <input type="hidden" name="accion_arbol" value="actualizar">
                                <input type="hidden" name="arbol_id" value="<?php echo $arbol['id']; ?>">
                                <input type="text" name="tamano" value="<?php echo $arbol['tamano']; ?>" class="form-control me-2">
                                <input type="text" name="ubicacion_geografica" value="<?php echo $arbol['ubicacion_geografica']; ?>" class="form-control me-2">
                                <select name="estado" class="form-control me-2">
                                    <option value="Disponible" <?php if ($arbol['estado'] == 'Disponible') echo 'selected'; ?>>Disponible</option>
                                    <option value="Vendido" <?php if ($arbol['estado'] == 'Vendido') echo 'selected'; ?>>Vendido</option>
                                </select>
                                <button type="submit" class="btn btn-primary">Actualizar</button>
                                <button type="submit" formaction="admin.php" name="accion_arbol" value="eliminar" class="btn btn-danger">Eliminar</button>
                            </form>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
   
    <?php include 'inc/footer.php'; ?>

    <!-- Vincula el JS de Bootstrap al final del body -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
