<?php
require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $tipo = $_POST['tipo'];

    // Por defecto todos son usuarios normales
    $rol = 'usuario';

    $query = "INSERT INTO usuarios (nombre, email, password, tipo, rol) VALUES (?, ?, ?, ?, ?)";
    $stmt = $con->prepare($query);
    $stmt->bind_param("sssss", $nombre, $email, $password, $tipo, $rol);
    
    if ($stmt->execute()) {
        header('Location: login.php?registro=exito');
        exit;
    } else {
        $error = "Error al registrar el usuario";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Registro</title>
	<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
	<div class="min-h-screen flex items-center justify-center">
		<div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
			<h1 class="text-2xl font-bold text-center mb-6">Registro de Usuario</h1>

			<?php if (isset($error)): ?>
			<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
				<?= $error ?>
			</div>
			<?php endif; ?>

			<form method="POST" class="space-y-4">
				<div>
					<label for="nombre" class="block text-gray-700 mb-2">Nombre Completo</label>
					<input type="text" id="nombre" name="nombre" required
						class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
				</div>

				<div>
					<label for="email" class="block text-gray-700 mb-2">Correo Electrónico</label>
					<input type="email" id="email" name="email" required
						class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
				</div>

				<div>
					<label for="password" class="block text-gray-700 mb-2">Contraseña</label>
					<input type="password" id="password" name="password" required
						class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
				</div>

				<div>
					<label for="tipo" class="block text-gray-700 mb-2">Tipo de Usuario</label>
					<select id="tipo" name="tipo" required
						class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
						<option value="estudiante">Estudiante</option>
						<option value="profesor">Profesor</option>
						<option value="personal">Personal Administrativo</option>
					</select>
				</div>

				<button type="submit"
					class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg transition duration-300">
					Registrarse
				</button>
			</form>

			<div class="mt-4 text-center">
				<a href="login.php" class="text-blue-500 hover:underline">¿Ya tienes cuenta? Inicia sesión</a>
			</div>
		</div>
	</div>
</body>

</html>