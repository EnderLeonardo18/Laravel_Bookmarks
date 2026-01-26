<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Iniciar Sesión</title>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">

    <div class="max-w-md w-full bg-white p-8 rounded-lg shadow-lg">
        <h2 class="text-3xl font-bold mb-6 text-center text-gray-800">Iniciar Sesión</h2>
        <p class="text-center text-gray-600 mb-8">Ingresa tus credenciales para acceder</p>

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Correo Electrónico</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="correo@ejemplo.com" required>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">Contraseña</label>
                <input type="password" name="password"
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="********" required>
            </div>

            <button type="submit"
                    class="w-full bg-blue-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-300">
                Entrar
            </button>
        </form>

        <p class="mt-6 text-center text-gray-500 text-sm">
            ¿No tienes cuenta? <a href="{{ route('users.create') }}" class="text-blue-500 hover:underline">Regístrate aquí</a>
        </p>
    </div>

</body>
</html>
