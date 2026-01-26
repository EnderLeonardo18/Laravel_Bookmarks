<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Crear Usuario</title>
</head>

<body class="bg-gray-100 p-8">
    <div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold mb-6">Nuevo Usuario</h2>

        <form action="{{ route('users.store') }}" method="POST">
            @csrf <div class="mb-4">
                <label class="block font-medium">Nombres:</label>
                <input type="text" name="first_name" value="{{ old('first_name') }}"
                    class="w-full border p-2 rounded">
                @error('first_name')
                    <p class="text-red-500 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block font-medium">Apellidos:</label>
                <input type="text" name="last_name" value="{{ old('last_name') }}" class="w-full border p-2 rounded">
                @error('last_name')
                    <p class="text-red-500 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block font-medium">Nombre de Usuario:</label>
                <input type="text" name="username" value="{{ old('username') }}" class="w-full border p-2 rounded">
                @error('username')
                    <p class="text-red-500 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block font-medium">Email:</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full border p-2 rounded">
                @error('email')
                    <p class="text-red-500 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block font-medium">Contraseña:</label>
                <input type="password" name="password" class="w-full border p-2 rounded">
                @error('password')
                    <p class="text-red-500 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block font-medium">Confirmar Contraseña:</label>
                <input type="password" name="password_confirmation" class="w-full border p-2 rounded">
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Guardar</button>
                <a href="{{ route('users.index') }}" class="text-gray-600 py-2">Cancelar</a>
            </div>
        </form>
    </div>
</body>

</html>
