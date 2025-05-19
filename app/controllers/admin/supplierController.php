<?php
require_once __DIR__.'/../../models/Supplier.php';

class SupplierController {
    private $supplierModel;

    public function __construct() {
        $this->supplierModel = new Supplier();
    }

    public function handleRequest() {
        $action = $_GET['action'] ?? '';

        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'add') {
                $this->handleAddSupplier();
            } elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'delete') {
                $this->handleDeleteSupplier();
            }
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    public function getSupplierr() {
        return $this->supplierModel->getAll();
    }

private function handleAddsupplier() {
    $required = ['id', 'nombre_contacto', 'nombre_empresa', 'direccion', 'tipo_rif'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("El campo $field es requerido");
        }
    }

    $rif = (int) $_POST['id'];
    $nombre_contacto = htmlspecialchars(trim($_POST['nombre_contacto']));
    $nombre_empresa = htmlspecialchars(trim($_POST['nombre_empresa']));
    $direccion = htmlspecialchars(trim($_POST['direccion']));
    $tipo_rif = htmlspecialchars(trim($_POST['tipo_rif']));

    // ✅ Verifica si ya existe antes de insertar
    if ($this->supplierModel->supplierExists($rif)) {
        header("Location: supplier-admin.php?error=rif_duplicado&rif=" . urlencode($rif));
        exit();
    }

    // ✅ Si no existe, lo insertas
    $success = $this->supplierModel->add($rif, $nombre_contacto, $nombre_empresa, $direccion, $tipo_rif);

    if ($success) {
        header("Location: supplier-admin.php?success=add");
        exit();
    }
}

    private function handleDeleteSupplier() {
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            throw new Exception("ID de producto inválido");
        }

        $success = $this->supplierModel->delete((int)$_GET['id']);

        if ($success) {
        header('Location: supplier-admin.php?success=delete');
            exit();
        }
    }
}

// Instanciar y ejecutar
$controller = new SupplierController();
$controller->handleRequest();
$supplierr = $controller->getSupplierr();