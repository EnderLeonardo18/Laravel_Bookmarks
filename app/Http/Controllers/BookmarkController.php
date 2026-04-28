<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookmarkRequest;
use App\Http\Requests\UpdateBookmarkRequest;
use App\Models\Bookmark;
use Embed\Embed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookmarkController extends Controller
{

    public function index(Request $request)
    {
        // FILTRO DE PRIVACIDAD: Solo los del usuario autenticado
        // Al usar $request->user(), la extensión suele reconocerlo mejor.
        // Esto garantiza que el Usuario A nunca vea los de B
        $bookmarks = $request->user()
        ->bookmarks()
        ->orderBy('order', 'asc')  // Cambio importante
        ->get();
        return response()->json($bookmarks);
    }

    public function store(StoreBookmarkRequest $request) {

        // El user_id se toma directamente del token de sesión por seguridad
        // Creamos el marcador a través de la relación del usuario
        // Así el user_id se guarda solo y es imposible suplantarlo
        $bookmark = $request->user()->bookmarks()->create($request->validated());
        return response()->json($bookmark, 201);
    }


    /**
     * Update the specified resource in storage.
     * Aquí permitimos editar URL, Título, etc.
     */
    public function update(UpdateBookmarkRequest $request, Bookmark $bookmark)
    {
        $this->authorizeOwner($bookmark);

        $bookmark->update($request->validated());

        return response()->json($bookmark);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bookmark $bookmark) {
        $this->authorizeOwner($bookmark);

        $bookmark->delete();
        return response()->json([
            'Message' => 'Marcador eliminado' // Clave 'Message' igual que en UserController
        ]);
    }



    public function reorder(Request $request)
    {
        $request->validate([
            'ordered_ids' => 'required|array',
            'ordered_ids.*' => 'exists:bookmarks,id'
        ]);

        $user = $request->user();
        if (!$user) {
            abort(401, 'No autenticado');
        }

        $isAdmin = $user->isAdmin();  // ✅ Método definido en User
        $userId = $user->id;          // ✅ Obtenemos el ID directamente

        foreach ($request->ordered_ids as $index => $id) {
            $bookmark = Bookmark::find($id);
            if (!$bookmark) {
                continue; // Si no existe, saltamos
            }

            if ($isAdmin || $bookmark->user_id === $userId) {
                $bookmark->update(['order' => $index]);
            } else {
                abort(403, 'No autorizado para reordenar el marcador ID: ' . $id);
            }
        }

        return response()->json(['message' => 'Orden actualizado correctamente']);
    }





    /**
     * Método de validación de propiedad.
     * Similar a cómo en UserController podrías validar permisos.
     */
    private function authorizeOwner(Bookmark $bookmark)
    {
        if ($bookmark->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }
    }


    public function allBookmarksAdmin()
    {
        // Usamos with('user') para que Angular reciba también el nombre del dueño
        $bookmarks = Bookmark::with('user')
        ->orderBy('order', 'asc')
        ->get();
        return response()->json($bookmarks);
    }

}
