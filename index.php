<?php
require_once 'db.php';
 
// 1. Agregar un artículo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nuevo_item']) && !empty(trim($_POST['nuevo_item']))) {
    $item = trim($_POST['nuevo_item']);
    $cantidad = (int)($_POST['cantidad'] ?? 1);
   
    $stmt = $pdo->prepare("INSERT INTO compras (item, cantidad) VALUES (?, ?)");
    $stmt->execute([$item, $cantidad]);
    header("Location: index.php");
    exit;
}
 
// 2. Marcar como comprado / pendiente
if (isset($_GET['completar'])) {
    $id = (int)$_GET['completar'];
    $estado = (int)$_GET['estado'];
    $nuevo_estado = ($estado === 1) ? 0 : 1;
   
    $stmt = $pdo->prepare("UPDATE compras SET comprado = ? WHERE id = ?");
    $stmt->execute([$nuevo_estado, $id]);
    header("Location: index.php");
    exit;
}
 
// 3. Eliminar un artículo
if (isset($_GET['eliminar'])) {
    $id = (int)$_GET['eliminar'];
   
    $stmt = $pdo->prepare("DELETE FROM compras WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: index.php");
    exit;
}
 
// 4. Obtener todos los artículos de la lista
$stmt = $pdo->query("SELECT * FROM compras ORDER BY comprado ASC, fecha_creacion DESC");
$articulos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
 
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Lista de Compras</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f6f9; color: #333; margin: 0; padding: 20px; display: flex; justify-content: center; }
        .container { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); width: 100%; max-width: 500px; }
        h1 { margin-top: 0; text-align: center; color: #0078d4; }
        form { display: flex; gap: 10px; margin-bottom: 20px; }
        input[type="text"] { flex: 3; padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
        input[type="number"] { flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 4px; text-align: center; }
        button { background: #0078d4; color: white; border: none; padding: 10px 15px; border-radius: 4px; cursor: pointer; font-weight: bold; }
        button:hover { background: #005a9e; }
        ul { list-style: none; padding: 0; margin: 0; }
        li { display: flex; justify-content: space-between; align-items: center; padding: 12px; border-bottom: 1px solid #eee; }
        .item-texto { flex-grow: 1; cursor: pointer; }
        .tachado { text-decoration: line-through; color: #888; }
        .cantidad { background: #e1dfdd; padding: 2px 8px; border-radius: 12px; font-size: 0.85em; margin-right: 15px; }
        .btn-eliminar { color: #d83b01; text-decoration: none; font-weight: bold; padding: 5px; }
        .btn-eliminar:hover { color: #a80000; }
    </style>
</head>
<body>
 
<div class="container">
    <h1>🛒 Lista de Compras</h1>
 
    <!-- Formulario para agregar ítems -->
    <form action="index.php" method="POST">
        <input type="text" name="nuevo_item" placeholder="Ej. Leche, Manzanas..." required>
        <input type="number" name="cantidad" value="1" min="1" title="Cantidad">
        <button type="submit">Agregar</button>
    </form>
 
    <!-- Listado -->
    <ul>
        <?php foreach ($articulos as $art): ?>
            <li>
                <span class="cantidad">x<?php echo $art['cantidad']; ?></span>
                <span class="item-texto <?php echo $art['comprado'] ? 'tachado' : ''; ?>"
                      onclick="window.location.href='index.php?completar=<?php echo $art['id']; ?>&estado=<?php echo $art['comprado']; ?>'">
                    <?php echo htmlspecialchars($art['item']); ?>
                </span>
                <a href="index.php?eliminar=<?php echo $art['id']; ?>" class="btn-eliminar" onclick="return confirm('¿Eliminar este artículo?')">✕</a>
            </li>
        <?php endforeach; ?>
        <?php if (empty($articulos)): ?>
            <li style="justify-content: center; color: #888; border: none;">La lista está vacía.</li>
        <?php endif; ?>
    </ul>
</div>
 
</body>
</html>