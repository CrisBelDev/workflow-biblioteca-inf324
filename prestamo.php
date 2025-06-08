<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $libro_id = $_POST['libro_id'];
    $fecha_devolucion = date('Y-m-d', strtotime('+2 weeks'));
    $usuario_id = $_SESSION['usuario_id'];

    $query = "INSERT INTO prestamos (libro_id, usuario_id, fecha_prestamo, fecha_devolucion) 
              VALUES (?, ?, NOW(), ?)";
    $stmt = $con->prepare($query);
    $stmt->bind_param("iis", $libro_id, $usuario_id, $fecha_devolucion);
    
    if ($stmt->execute()) {
        header('Location: mis-prestamos.php?prestamo=exito');
        exit;
    } else {
        $error = "Error al registrar el préstamo";
    }
}

// Obtener listado de libros disponibles
$query = "SELECT id, titulo, autor FROM libros WHERE disponible = 1";
$result = $con->query($query);
$libros = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Solicitar Préstamo</title>
	<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
	<div class="container mx-auto px-4 py-8">
		<div class="bg-white rounded-lg shadow-md p-6 max-w-4xl mx-auto">
			<div class="flex justify-between items-center mb-6">
				<h1 class="text-2xl font-bold text-gray-800">Solicitar Préstamo</h1>
				<a href="index.php" class="text-blue-500 hover:underline">Volver al inicio</a>
			</div>

			<?php if (isset($error)): ?>
			<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
				<?= $error ?>
			</div>
			<?php endif; ?>

			<form method="POST" class="space-y-4">
				<div>
					<label for="libro_id" class="block text-gray-700 mb-2">Seleccione un libro</label>
					<select id="libro_id" name="libro_id" required
						class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
						<option value="">-- Seleccione --</option>
						<?php foreach ($libros as $libro): ?>
						<option value="<?= $libro['id'] ?>">
							<?= htmlspecialchars($libro['titulo']) ?> - <?= htmlspecialchars($libro['autor']) ?>
						</option>
						<?php endforeach; ?>
					</select>
				</div>

				<button type="submit"
					class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg transition duration-300">
					Solicitar Préstamo
				</button>
			</form>
		</div>
	</div>
</body>

</html>