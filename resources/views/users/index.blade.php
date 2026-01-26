<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Lista de Usuarios</title>
</head>

<body class="bg-gray-100 p-4 md:p-8">

    <div class="max-w-6xl mx-auto bg-white p-6 rounded-lg shadow-md">

        <div class="flex flex-col md:flex-row justify-between items-center mb-8 border-b pb-4 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Usuarios Registrados</h2>
                <p class="text-gray-500 text-sm">Bienvenido, <span class="font-semibold">{{ auth()->user()->first_name }}</span></p>
            </div>

            <div class="flex items-center gap-4">
                {{-- 1. SOLO EL ADMIN ve el botón de crear --}}
                @can('admin-access')
                    <a href="{{ route('users.create') }}"
                        class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition shadow-sm font-medium whitespace-nowrap">
                        + Nuevo Usuario
                    </a>
                @endcan

                {{-- 2. TODOS (incluyendo el user normal) pueden cerrar sesión --}}
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                        class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition shadow-sm font-medium whitespace-nowrap">
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        </div>

        @if (session('Success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 shadow-sm" role="alert">
                <p class="font-bold">¡Logrado!</p>
                <p>{{ session('Success') }}</p>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full border-collapse bg-white">
                <thead>
                    <tr class="bg-gray-800 text-white text-left">
                        <th class="p-3 border">Nombres</th>
                        <th class="p-3 border">Apellidos</th>
                        <th class="p-3 border">Usuario</th>
                        <th class="p-3 border">Email</th>
                        <th class="p-3 border">Rol</th>
                        <th class="p-3 border text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-3 border">{{ $user->first_name }}</td>
                            <td class="p-3 border">{{ $user->last_name }}</td>
                            <td class="p-3 border">{{ $user->username }}</td>
                            <td class="p-3 border">{{ $user->email }}</td>
                            <td class="p-3 border">
                                <span class="px-2 py-1 rounded text-xs font-bold {{ $user->role->value === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ strtoupper($user->role->value) }}
                                </span>
                            </td>
                            <td class="p-3 border">
                                <div class="flex justify-center gap-3">
                                    @can('admin-access')
                                        <a href="{{ route('users.edit', $user) }}"
                                            class="bg-blue-100 text-blue-600 px-3 py-1 rounded hover:bg-blue-200 transition font-bold">
                                            Editar
                                        </a>

                                        <form action="{{ route('users.destroy', $user) }}" method="POST"
                                            onsubmit="return confirm('¿Estás seguro?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="bg-red-100 text-red-600 px-3 py-1 rounded hover:bg-red-200 transition font-bold">
                                                Eliminar
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-gray-400 italic text-sm">Solo lectura</span>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-gray-500">
                                No hay usuarios registrados actualmente.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $users->links() }}
        </div>
    </div>

</body>
</html>
