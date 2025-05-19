<?php
// Incluimos el controlador y obtenemos los productos
require_once __DIR__.'/../../controllers/Admin/supplierController.php';

$controller = new SupplierController();

$action = $_GET['action'] ?? null;

switch ($action) {
    case 'delete':
        if (isset($_GET['id'])) {
            $controller->deleteSupplier($_GET['id']);
            header('Location: supplier-admin.php?success=delete');
            exit;
        }
        break;

    case 'add':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->addSupplier(
            $_POST['id'], 
            $_POST['nombre_contacto'],
            $_POST['nombre_empresa'], 
            $_POST['direccion'], 
            $_POST['tipo_rif']);
            header('Location: supplier-admin.php?success=add');
            exit;
        }
        break;

    default:
        $supplier = $controller->getSupplierr();
        break;


}// maneja add/delete/truncate
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proveedores - Garage Barki</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Favicon -->
    <link rel="shortcut icon" href="../../../public/assets/icons/Logo - Garage Barki.webp" type="image/x-icon">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../../public/assets/css/admin-styles.css">
</head>
<body>

        <nav class="sidebar" id="sidebar">
        <div class="sidebar-sticky">
            <div class="sidebar-header">
                <h3>GARAGE<span>BARKI</span></h3>
                <p class="mb-0">Panel de Administración</p>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="#Profe_No_Hago_Nada">
                        <i class="fas fa-tachometer-alt"></i>
                        Inicio
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/app/views/admin/products-admin.php">
                        <i class="fas fa-tshirt"></i>
                        Productos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="/app/views/admin/supplier-admin.php">
                        <i class="fas fa-shopping-cart"></i>
                        Proveedores
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/app/views/admin/clients-admin.php">
                        <i class="fas fa-users"></i>
                        Clientes
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="display-6 fw-bold text-dark">Proveedores</h1>
            </div>
            <button class="btn btn-primary rounded-pill px-4 me-3" data-bs-toggle="modal" data-bs-target="#addProductModal">
                <i class="fas fa-plus me-1"></i> Agregar contacto
            </button>
            
            <!-- Mensajes de éxito/error -->
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success mt-3">
                    <?php 
                    switch($_GET['success']) {
                        case 'add': echo 'Producto agregado correctamente'; break;
                        case 'delete': echo 'Producto eliminado correctamente'; break;
                    }
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger mt-3">
                    <?php 
                    if ($_GET['error'] === 'rif_duplicado') {
                        $rif = isset($_GET['rif']) ? htmlspecialchars($_GET['rif']) : '[RIF no proporcionado]';
                        echo "Error: El RIF <strong>$rif</strong> ya está registrado.";
                    }
                    ?>
                </div>
            <?php endif; ?>

            <!-- Tabla de Productos -->
            <div class="card mt-3">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle text-center" >
                            <thead>
                                <tr>
                                    <th>Rif</th>
                                    <th>Contacto</th>
                                    <th>Empresa</th>
                                    <th>Dirección</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($supplier)): ?>
                                    <?php foreach ($supplier as $supplier): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($supplier['tipo_rif'] ?? '') ?>-<?= htmlspecialchars($supplier['id'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($supplier['nombre_contacto'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($supplier['nombre_empresa'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($supplier['direccion'] ?? '') ?></td>
                                            <td>
                                                <a href="supplier-admin.php?action=delete&id=<?= $supplier['id'] ?>"
                                                   class="btn btn-sm btn-outline-danger"
                                                   onclick="return confirm('¿Estás seguro de eliminar este producto?')">
                                                    <i class="fas fa-trash"></i> Eliminar
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            <div class="alert alert-info mb-0">No hay productos disponibles</div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Añadir Producto -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModalLabel">Añadir Nuevo Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="supplier-admin.php?action=add" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre del Proveedor</label>
                            <input type="text" class="form-control" 
                                name="nombre_contacto" 
                                placeholder="Ingrese nombre" 
                                pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$"
                                oninput="this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, '');"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Empresa</label>
                            <input type="text" class="form-control" name="nombre_empresa" placeholder="Ingrese nombre" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rif</label>
                            <input type="text" class="form-control" 
                                name="id" 
                                placeholder="Ingrese rif" 
                                pattern="\d{9}" 
                                maxlength="9" minlength="9"
                                inputmode="numeric"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,9);"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tipo del rif</label>
                            <select class="form-select" name="tipo_rif" required>
                                <option value="J">J</option>
                                <option value="G">G</option>
                                <option value="C">C</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Direccion</label>
                            <input type="text" class="form-control" name="direccion" placeholder="Ingrese direccion" required>
                        </div>



                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>




    

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>