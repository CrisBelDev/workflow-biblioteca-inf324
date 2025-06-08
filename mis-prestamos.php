<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Obtener préstamos activos del usuario (no devueltos)
$query_activos = "SELECT p.id, l.titulo, l.autor, p.fecha_prestamo, p.fecha_devolucion 
                 FROM prestamos p
                 JOIN libros l ON p.libro_id = l.id
                 WHERE p.usuario_id = ? AND p.devuelto = FALSE
                 ORDER BY p.fecha_devolucion ASC";  // Ordenar por fecha de devolución próxima

$stmt = $con->prepare($query_activos);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$prestamos_activos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Obtener préstamos devueltos (histórico)
$query_devueltos = "SELECT p.id, l.titulo, l.autor, p.fecha_prestamo, p.fecha_devolucion, p.fecha_devolucion_real
                   FROM prestamos p
                   JOIN libros l ON p.libro_id = l.id
                   WHERE p.usuario_id = ? AND p.devuelto = TRUE
                   ORDER BY p.fecha_devolucion_real DESC
                   LIMIT 10";  // Mostrar solo los 10 más recientes

$stmt = $con->prepare($query_devueltos);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$prestamos_devueltos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Mis Préstamos</title>
	<script src="https://cdn.tailwindcss.com"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-100">
	<div class="container mx-auto px-4 py-8">
		<div class="bg-white rounded-lg shadow-md p-6 max-w-4xl mx-auto">
			<div class="flex justify-between items-center mb-6">
				<h1 class="text-2xl font-bold text-gray-800">Mis Préstamos</h1>
				<a href="index.php" class="text-blue-500 hover:underline">Volver al inicio</a>
			</div>

			<?php if (isset($_GET['prestamo']) && $_GET['prestamo'] === 'exito'): ?>
			<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
				Préstamo registrado exitosamente
			</div>
			<?php endif; ?>

			<!-- Pestañas -->
			<div class="flex border-b mb-6">
				<button id="tab-activos" class="px-4 py-2 font-medium text-blue-600 border-b-2 border-blue-600">
					<i class="fas fa-clock mr-1"></i> Préstamos Activos
				</button>
				<button id="tab-devueltos" class="px-4 py-2 font-medium text-gray-500 hover:text-gray-700">
					<i class="fas fa-check-circle mr-1"></i> Historial de Préstamos
				</button>
			</div>

			<!-- Sección de Préstamos Activos -->
			<div id="seccion-activos">
				<h2 class="text-xl font-semibold mb-4">Préstamos en Curso</h2>

				<?php if (empty($prestamos_activos)): ?>
				<p class="text-gray-600">No tienes préstamos activos actualmente.</p>
				<?php else: ?>
				<div class="overflow-x-auto">
					<table class="min-w-full bg-white">
						<thead>
							<tr>
								<th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-gray-600">Libro
								</th>
								<th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-gray-600">Autor
								</th>
								<th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-gray-600">Fecha
									Préstamo</th>
								<th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-gray-600">Fecha
									Devolución</th>
								<th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-gray-600">Estado
								</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($prestamos_activos as $prestamo): 
                                    $estaVencido = strtotime($prestamo['fecha_devolucion']) < time();
                                ?>
							<tr class="<?= $estaVencido ? 'bg-red-50' : '' ?>">
								<td class="py-2 px-4 border-b border-gray-200">
									<?= htmlspecialchars($prestamo['titulo']) ?></td>
								<td class="py-2 px-4 border-b border-gray-200">
									<?= htmlspecialchars($prestamo['autor']) ?></td>
								<td class="py-2 px-4 border-b border-gray-200">
									<?= date('d/m/Y', strtotime($prestamo['fecha_prestamo'])) ?></td>
								<td class="py-2 px-4 border-b border-gray-200">
									<?= date('d/m/Y', strtotime($prestamo['fecha_devolucion'])) ?>
									<?php if ($estaVencido): ?>
									<span class="text-xs text-red-500">(Vencido)</span>
									<?php endif; ?>
								</td>
								<td class="py-2 px-4 border-b border-gray-200">
									<?php if ($estaVencido): ?>
									<span class="text-red-500 font-medium">¡Por devolver!</span>
									<?php else: ?>
									<span class="text-yellow-600">En préstamo</span>
									<?php endif; ?>
								</td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				<?php endif; ?>
			</div>

			<!-- Sección de Préstamos Devueltos -->
			<div id="seccion-devueltos" class="hidden">
				<h2 class="text-xl font-semibold mb-4">Préstamos Devueltos</h2>

				<?php if (empty($prestamos_devueltos)): ?>
				<p class="text-gray-600">No tienes préstamos devueltos recientemente.</p>
				<?php else: ?>
				<div class="overflow-x-auto">
					<table class="min-w-full bg-white">
						<thead>
							<tr>
								<th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-gray-600">Libro
								</th>
								<th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-gray-600">Autor
								</th>
								<th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-gray-600">Fecha
									Préstamo</th>
								<th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-gray-600">
									Devuelto el</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($prestamos_devueltos as $prestamo): ?>
							<tr>
								<td class="py-2 px-4 border-b border-gray-200">
									<?= htmlspecialchars($prestamo['titulo']) ?></td>
								<td class="py-2 px-4 border-b border-gray-200">
									<?= htmlspecialchars($prestamo['autor']) ?></td>
								<td class="py-2 px-4 border-b border-gray-200">
									<?= date('d/m/Y', strtotime($prestamo['fecha_prestamo'])) ?></td>
								<td class="py-2 px-4 border-b border-gray-200">
									<?= date('d/m/Y H:i', strtotime($prestamo['fecha_devolucion_real'])) ?></td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<script>
	// Funcionalidad de las pestañas
	document.getElementById('tab-activos').addEventListener('click', function() {
		document.getElementById('seccion-activos').classList.remove('hidden');
		document.getElementById('seccion-devueltos').classList.add('hidden');
		this.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');
		this.classList.remove('text-gray-500');
		document.getElementById('tab-devueltos').classList.add('text-gray-500');
		document.getElementById('tab-devueltos').classList.remove('text-blue-600', 'border-b-2',
		'border-blue-600');
	});

	document.getElementById('tab-devueltos').addEventListener('click', function() {
		document.getElementById('seccion-activos').classList.add('hidden');
		document.getElementById('seccion-devueltos').classList.remove('hidden');
		this.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');
		this.classList.remove('text-gray-500');
		document.getElementById('tab-activos').classList.add('text-gray-500');
		document.getElementById('tab-activos').classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
	});
	</script>
</body>

</html>