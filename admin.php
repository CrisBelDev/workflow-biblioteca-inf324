<?php
session_start();
require 'conexion.php';

// Verificar si es administrador
/* if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header('Location: login.php');
    exit;
} */

// Procesar formulario de libro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['crear_libro'])) {
        // Crear nuevo libro
        $titulo = $_POST['titulo'];
        $autor = $_POST['autor'];
        $isbn = $_POST['isbn'];
        
        $query = "INSERT INTO libros (titulo, autor, isbn, disponible) VALUES (?, ?, ?, 1)";
        $stmt = $con->prepare($query);
        $stmt->bind_param("sss", $titulo, $autor, $isbn);
        $stmt->execute();
    } elseif (isset($_POST['actualizar_libro'])) {
        // Actualizar libro existente
        $id = $_POST['id'];
        $titulo = $_POST['titulo'];
        $autor = $_POST['autor'];
        $isbn = $_POST['isbn'];
        $disponible = isset($_POST['disponible']) ? 1 : 0;
        
        $query = "UPDATE libros SET titulo = ?, autor = ?, isbn = ?, disponible = ? WHERE id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("sssii", $titulo, $autor, $isbn, $disponible, $id);
        $stmt->execute();
    } elseif (isset($_GET['eliminar'])) {
        // Eliminar libro
        $id = $_GET['eliminar'];
        $query = "DELETE FROM libros WHERE id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
}

// Obtener todos los libros
$query = "SELECT * FROM libros ORDER BY titulo";
$libros = $con->query($query)->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Panel de Administración</title>
	<script src="https://cdn.tailwindcss.com"></script>
	<!-- Iconos de Font Awesome -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-100">
	<div class="container mx-auto px-4 py-8">
		<!-- Header -->
		<div class="flex justify-between items-center mb-8">
			<h1 class="text-3xl font-bold text-gray-800">
				<i class="fas fa-book mr-2"></i> Panel de Administración
			</h1>
			<div>
				<a href="index.php" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded mr-2">
					<i class="fas fa-home mr-1"></i> Inicio
				</a>
				<a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
					<i class="fas fa-sign-out-alt mr-1"></i> Salir
				</a>
			</div>
		</div>

		<!-- Pestañas -->
		<div class="flex border-b mb-6">
			<a href="admin.php" class="px-4 py-2 font-medium text-blue-600 border-b-2 border-blue-600">
				<i class="fas fa-book mr-1"></i> Libros
			</a>
			<a href="admin-usuarios.php" class="px-4 py-2 font-medium text-gray-500 hover:text-gray-700">
				<i class="fas fa-users mr-1"></i> Usuarios
			</a>
			<a href="admin-prestamos.php" class="px-4 py-2 font-medium text-gray-500 hover:text-gray-700">
				<i class="fas fa-exchange-alt mr-1"></i> Préstamos
			</a>
		</div>

		<!-- Formulario para agregar/editar libro -->
		<div class="bg-white rounded-lg shadow-md p-6 mb-8">
			<h2 class="text-xl font-semibold mb-4">
				<?= isset($_GET['editar']) ? 'Editar Libro' : 'Agregar Nuevo Libro' ?>
			</h2>

			<form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
				<?php if (isset($_GET['editar'])): 
                    $id = $_GET['editar'];
                    $libro = $con->query("SELECT * FROM libros WHERE id = $id")->fetch_assoc();
                ?>
				<input type="hidden" name="id" value="<?= $libro['id'] ?>">
				<?php endif; ?>

				<div>
					<label class="block text-gray-700 mb-2">Título</label>
					<input type="text" name="titulo" required
						value="<?= isset($libro) ? htmlspecialchars($libro['titulo']) : '' ?>"
						class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
				</div>

				<div>
					<label class="block text-gray-700 mb-2">Autor</label>
					<input type="text" name="autor" required
						value="<?= isset($libro) ? htmlspecialchars($libro['autor']) : '' ?>"
						class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
				</div>

				<div>
					<label class="block text-gray-700 mb-2">ISBN</label>
					<input type="text" name="isbn" value="<?= isset($libro) ? htmlspecialchars($libro['isbn']) : '' ?>"
						class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
				</div>

				<?php if (isset($_GET['editar'])): ?>
				<div class="flex items-center">
					<input type="checkbox" name="disponible" id="disponible"
						<?= $libro['disponible'] ? 'checked' : '' ?> class="mr-2 h-5 w-5">
					<label for="disponible" class="text-gray-700">Disponible para préstamo</label>
				</div>

				<div class="md:col-span-2 flex justify-end space-x-2">
					<a href="admin.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
						Cancelar
					</a>
					<button type="submit" name="actualizar_libro"
						class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
						<i class="fas fa-save mr-1"></i> Actualizar
					</button>
				</div>
				<?php else: ?>
				<div class="md:col-span-2 flex justify-end">
					<button type="submit" name="crear_libro"
						class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
						<i class="fas fa-plus mr-1"></i> Agregar Libro
					</button>
				</div>
				<?php endif; ?>
			</form>
		</div>

		<!-- Listado de libros -->
		<div class="bg-white rounded-lg shadow-md overflow-hidden">
			<div class="px-6 py-4 border-b">
				<h2 class="text-xl font-semibold">Listado de Libros</h2>
			</div>

			<div class="overflow-x-auto">
				<table class="min-w-full">
					<thead class="bg-gray-50">
						<tr>
							<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Título</th>
							<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Autor</th>
							<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ISBN</th>
							<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
							<th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-gray-200">
						<?php foreach ($libros as $libro): ?>
						<tr>
							<td class="px-6 py-4 whitespace-nowrap">
								<?= htmlspecialchars($libro['titulo']) ?>
							</td>
							<td class="px-6 py-4 whitespace-nowrap">
								<?= htmlspecialchars($libro['autor']) ?>
							</td>
							<td class="px-6 py-4 whitespace-nowrap">
								<?= $libro['isbn'] ? htmlspecialchars($libro['isbn']) : 'N/A' ?>
							</td>
							<td class="px-6 py-4 whitespace-nowrap">
								<span
									class="px-2 py-1 text-xs rounded-full 
                                    <?= $libro['disponible'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
									<?= $libro['disponible'] ? 'Disponible' : 'Prestado' ?>
								</span>
							</td>
							<td class="px-6 py-4 whitespace-nowrap text-right">
								<a href="admin.php?editar=<?= $libro['id'] ?>"
									class="text-blue-500 hover:text-blue-700 mr-3" title="Editar">
									<i class="fas fa-edit"></i>
								</a>
								<a href="admin.php?eliminar=<?= $libro['id'] ?>" class="text-red-500 hover:text-red-700"
									title="Eliminar" onclick="return confirm('¿Eliminar este libro permanentemente?')">
									<i class="fas fa-trash-alt"></i>
								</a>
							</td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>

			<?php if (empty($libros)): ?>
			<div class="px-6 py-8 text-center text-gray-500">
				No hay libros registrados en el sistema
			</div>
			<?php endif; ?>
		</div>
	</div>

	<script>
	// Confirmación antes de eliminar
	document.querySelectorAll('a[href*="eliminar"]').forEach(link => {
		link.addEventListener('click', function(e) {
			if (!confirm('¿Estás seguro de eliminar este libro?')) {
				e.preventDefault();
			}
		});
	});
	</script>
</body>

</html>