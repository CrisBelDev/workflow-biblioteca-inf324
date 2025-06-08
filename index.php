<?php
session_start();
require 'conexion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Obtener información del usuario
$usuario_id = $_SESSION['usuario_id'];
$query = "SELECT nombre, rol FROM usuarios WHERE id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Sistema Bibliotecario</title>
	<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
	<div class="container mx-auto px-4 py-8">
		<div class="bg-white rounded-lg shadow-md p-6 max-w-4xl mx-auto">
			<div class="flex justify-between items-center mb-6">
				<h1 class="text-2xl font-bold text-gray-800">Sistema Bibliotecario</h1>
				<div class="flex items-center space-x-4">
					<span class="text-gray-600">Hola, <?= htmlspecialchars($usuario['nombre']) ?></span>
					<a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">Cerrar
						sesión</a>
				</div>
			</div>

			<div class="grid md:grid-cols-2 gap-6">
				<a href="prestamo.php"
					class="bg-blue-500 hover:bg-blue-600 text-white p-6 rounded-lg shadow transition duration-300">
					<h2 class="text-xl font-semibold mb-2">Solicitar Préstamo</h2>
					<p class="text-blue-100">Realiza una nueva solicitud de préstamo de libros</p>
				</a>

				<a href="mis-prestamos.php"
					class="bg-green-500 hover:bg-green-600 text-white p-6 rounded-lg shadow transition duration-300">
					<h2 class="text-xl font-semibold mb-2">Mis Préstamos</h2>
					<p class="text-green-100">Consulta tus préstamos activos y su estado</p>
				</a>

				<?php if ($usuario['rol'] === 'admin'): ?>
				<a href="admin.php"
					class="bg-purple-500 hover:bg-purple-600 text-white p-6 rounded-lg shadow transition duration-300">
					<h2 class="text-xl font-semibold mb-2">Panel de Administración</h2>
					<p class="text-purple-100">Gestiona usuarios y préstamos</p>
				</a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</body>

</html>