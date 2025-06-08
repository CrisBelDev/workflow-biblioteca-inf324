<?php
session_start();
require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT id, nombre, password, rol FROM usuarios WHERE email = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $usuario = $result->fetch_assoc();
        if (password_verify($password, $usuario['password'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_rol'] = $usuario['rol'];
            
            header('Location: index.php');
            exit;
        }
    }

    $error = "Credenciales incorrectas";
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Iniciar Sesión | Biblioteca</title>
	<script src="https://cdn.tailwindcss.com"></script>
	<script>
	tailwind.config = {
		darkMode: 'class',
		theme: {
			extend: {
				colors: {
					dark: {
						100: '#E5E7EB',
						200: '#D1D5DB',
						300: '#9CA3AF',
						400: '#6B7280',
						500: '#4B5563',
						600: '#374151',
						700: '#1F2937',
						800: '#111827',
						900: '#0F172A',
					}
				}
			}
		}
	}
	</script>
</head>

<body class="bg-gray-100 dark:bg-dark-900 transition-colors duration-300">
	<div class="min-h-screen flex items-center justify-center p-4">
		<div
			class="bg-white dark:bg-dark-800 p-8 rounded-xl shadow-2xl w-full max-w-md border border-gray-200 dark:border-dark-600 transition-colors duration-300">
			<div class="text-center mb-8">
				<h1 class="text-3xl font-bold text-gray-800 dark:text-dark-100 mb-2">Biblioteca Digital</h1>
				<p class="text-gray-600 dark:text-dark-300">Accede a tu cuenta</p>
			</div>

			<?php if (isset($error)): ?>
			<div
				class="bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-200 px-4 py-3 rounded-lg mb-6">
				<?= $error ?>
			</div>
			<?php endif; ?>

			<form method="POST" class="space-y-6">
				<div>
					<label for="email" class="block text-gray-700 dark:text-dark-200 mb-2 font-medium">Correo
						Electrónico</label>
					<input type="email" id="email" name="email" required
						class="w-full px-4 py-3 bg-gray-50 dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 dark:text-dark-100 placeholder-gray-400 dark:placeholder-dark-400 transition-colors duration-300"
						placeholder="tu@email.com">
				</div>

				<div>
					<label for="password"
						class="block text-gray-700 dark:text-dark-200 mb-2 font-medium">Contraseña</label>
					<input type="password" id="password" name="password" required
						class="w-full px-4 py-3 bg-gray-50 dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 dark:text-dark-100 placeholder-gray-400 dark:placeholder-dark-400 transition-colors duration-300"
						placeholder="••••••••">
				</div>

				<button type="submit"
					class="w-full bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600 text-white py-3 px-4 rounded-lg font-medium transition-all duration-300 transform hover:scale-[1.01] shadow-lg">
					Ingresar
				</button>
			</form>

			<div class="mt-6 pt-6 border-t border-gray-200 dark:border-dark-600 text-center">
				<a href="registro.php"
					class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium transition-colors duration-300">
					¿No tienes cuenta? Regístrate
				</a>
			</div>
		</div>
	</div>

	<!-- Botón para alternar dark/light mode -->
	<div class="fixed bottom-6 right-6">
		<button id="themeToggle"
			class="p-3 rounded-full bg-gray-200 dark:bg-dark-700 text-gray-800 dark:text-dark-200 shadow-lg hover:bg-gray-300 dark:hover:bg-dark-600 transition-colors duration-300">
			<svg id="sunIcon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
				stroke="currentColor">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
					d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
			</svg>
			<svg id="moonIcon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 hidden" fill="none" viewBox="0 0 24 24"
				stroke="currentColor">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
					d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
			</svg>
		</button>
	</div>

	<script>
	// Toggle dark mode
	const themeToggle = document.getElementById('themeToggle');
	const sunIcon = document.getElementById('sunIcon');
	const moonIcon = document.getElementById('moonIcon');

	// Comprobar preferencias del sistema
	if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia(
			'(prefers-color-scheme: dark)').matches)) {
		document.documentElement.classList.add('dark');
		sunIcon.classList.add('hidden');
		moonIcon.classList.remove('hidden');
	} else {
		document.documentElement.classList.remove('dark');
		sunIcon.classList.remove('hidden');
		moonIcon.classList.add('hidden');
	}

	// Alternar manualmente
	themeToggle.addEventListener('click', function() {
		document.documentElement.classList.toggle('dark');
		sunIcon.classList.toggle('hidden');
		moonIcon.classList.toggle('hidden');

		// Guardar preferencia
		if (document.documentElement.classList.contains('dark')) {
			localStorage.setItem('color-theme', 'dark');
		} else {
			localStorage.setItem('color-theme', 'light');
		}
	});
	</script>
</body>

</html>