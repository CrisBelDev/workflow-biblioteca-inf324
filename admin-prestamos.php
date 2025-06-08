<?php
session_start();
require 'conexion.php';

// Verificar si es administrador/bibliotecario
/* if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'bibliotecario')) {
    header('Location: login.php');
    exit;
} */

// Procesar devolución
if (isset($_GET['devolver'])) {
    $prestamo_id = $_GET['devolver'];
    
    $query = "UPDATE prestamos SET devuelto = TRUE, fecha_devolucion_real = NOW() 
              WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $prestamo_id);
    $stmt->execute();
    
    // Marcar libro como disponible
    $query = "UPDATE libros l
              JOIN prestamos p ON l.id = p.libro_id
              SET l.disponible = TRUE
              WHERE p.id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $prestamo_id);
    $stmt->execute();
    
    header('Location: admin-prestamos.php?success=devolucion');
    exit;
}

// Obtener todos los préstamos
$query = "SELECT p.id, p.fecha_prestamo, p.fecha_devolucion, p.devuelto, p.fecha_devolucion_real,
                 l.titulo, l.autor, l.isbn,
                 u.nombre AS usuario_nombre, u.email AS usuario_email
          FROM prestamos p
          JOIN libros l ON p.libro_id = l.id
          JOIN usuarios u ON p.usuario_id = u.id
          ORDER BY p.devuelto, p.fecha_devolucion";
$prestamos = $con->query($query)->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Gestión de Préstamos</title>
	<script src="https://cdn.tailwindcss.com"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-100">
	<div class="container mx-auto px-4 py-8">
		<!-- Header -->
		<div class="flex justify-between items-center mb-8">
			<h1 class="text-3xl font-bold text-gray-800">
				<i class="fas fa-exchange-alt mr-2"></i> Gestión de Préstamos
			</h1>
			<div>
				<a href="admin.php" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded mr-2">
					<i class="fas fa-book mr-1"></i> Libros
				</a>
				<a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
					<i class="fas fa-home mr-1"></i> Inicio
				</a>
			</div>
		</div>

		<!-- Mensajes -->
		<?php if (isset($_GET['success']) && $_GET['success'] === 'devolucion'): ?>
		<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
			Libro marcado como devuelto correctamente
		</div>
		<?php endif; ?>

		<!-- Pestañas -->
		<div class="flex border-b mb-6">
			<a href="admin-prestamos.php" class="px-4 py-2 font-medium text-blue-600 border-b-2 border-blue-600">
				<i class="fas fa-list mr-1"></i> Todos los Préstamos
			</a>
			<a href="admin-prestamos.php?estado=activos"
				class="px-4 py-2 font-medium text-gray-500 hover:text-gray-700">
				<i class="fas fa-clock mr-1"></i> Préstamos Activos
			</a>
			<a href="admin-prestamos.php?estado=vencidos"
				class="px-4 py-2 font-medium text-gray-500 hover:text-gray-700">
				<i class="fas fa-exclamation-triangle mr-1"></i> Préstamos Vencidos
			</a>
		</div>

		<!-- Listado de préstamos -->
		<div class="bg-white rounded-lg shadow-md overflow-hidden">
			<div class="px-6 py-4 border-b">
				<h2 class="text-xl font-semibold">Listado de Préstamos</h2>
			</div>

			<div class="overflow-x-auto">
				<table class="min-w-full">
					<thead class="bg-gray-50">
						<tr>
							<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Libro</th>
							<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuario</th>
							<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Préstamo
							</th>
							<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Devolución
							</th>
							<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
							<th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-gray-200">
						<?php foreach ($prestamos as $prestamo): ?>
						<tr class="<?= $prestamo['devuelto'] ? 'bg-gray-50' : '' ?>">
							<td class="px-6 py-4">
								<div class="font-medium"><?= htmlspecialchars($prestamo['titulo']) ?></div>
								<div class="text-sm text-gray-500"><?= htmlspecialchars($prestamo['autor']) ?></div>
								<?php if ($prestamo['isbn']): ?>
								<div class="text-xs text-gray-400">ISBN: <?= htmlspecialchars($prestamo['isbn']) ?>
								</div>
								<?php endif; ?>
							</td>
							<td class="px-6 py-4">
								<div class="font-medium"><?= htmlspecialchars($prestamo['usuario_nombre']) ?></div>
								<div class="text-sm text-gray-500"><?= htmlspecialchars($prestamo['usuario_email']) ?>
								</div>
							</td>
							<td class="px-6 py-4 whitespace-nowrap">
								<?= date('d/m/Y', strtotime($prestamo['fecha_prestamo'])) ?>
							</td>
							<td class="px-6 py-4 whitespace-nowrap">
								<?= date('d/m/Y', strtotime($prestamo['fecha_devolucion'])) ?>
								<?php if (!$prestamo['devuelto'] && strtotime($prestamo['fecha_devolucion']) < time()): ?>
								<span class="text-xs text-red-500">(Vencido)</span>
								<?php endif; ?>
							</td>
							<td class="px-6 py-4 whitespace-nowrap">
								<?php if ($prestamo['devuelto']): ?>
								<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
									<i class="fas fa-check mr-1"></i> Devuelto
								</span>
								<div class="text-xs text-gray-500 mt-1">
									<?= date('d/m/Y H:i', strtotime($prestamo['fecha_devolucion_real'])) ?>
								</div>
								<?php else: ?>
								<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
									<i class="fas fa-clock mr-1"></i> Activo
								</span>
								<?php endif; ?>
							</td>
							<td class="px-6 py-4 whitespace-nowrap text-right">
								<?php if (!$prestamo['devuelto']): ?>
								<a href="admin-prestamos.php?devolver=<?= $prestamo['id'] ?>"
									class="text-green-500 hover:text-green-700 mr-3" title="Marcar como devuelto"
									onclick="return confirm('¿Marcar este libro como devuelto?')">
									<i class="fas fa-check-circle"></i>
								</a>
								<?php endif; ?>
								<a href="#" class="text-blue-500 hover:text-blue-700" title="Ver detalles">
									<i class="fas fa-eye"></i>
								</a>
							</td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>

			<?php if (empty($prestamos)): ?>
			<div class="px-6 py-8 text-center text-gray-500">
				No hay préstamos registrados
			</div>
			<?php endif; ?>
		</div>
	</div>

	<script>
	// Confirmación antes de marcar como devuelto
	document.querySelectorAll('a[href*="devolver"]').forEach(link => {
		link.addEventListener('click', function(e) {
			if (!confirm('¿Estás seguro de marcar este libro como devuelto?')) {
				e.preventDefault();
			}
		});
	});
	</script>
</body>

</html>