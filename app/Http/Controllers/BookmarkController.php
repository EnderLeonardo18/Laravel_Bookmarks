<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookmarkRequest;
use App\Http\Requests\UpdateBookmarkRequest;
use App\Models\Bookmark;
use App\Services\EpisodeCheckerService;
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


    // Metodo que llama todos los marcadores en modo Admin
    public function allBookmarksAdmin()
    {
        // Usamos with('user') para que Angular reciba también el nombre del dueño
        $bookmarks = Bookmark::with('user')
        ->orderBy('order', 'asc')
        ->get();
        return response()->json($bookmarks);
    }


    public function checkEpisodes(int $id, EpisodeCheckerService $checker)
{
    try {
        $bookmark = Bookmark::findOrFail($id);

        // 1. PRIORIDAD: Si tiene 'progress_url' (Episodio actual), usa ese. Si no, usa 'url'.
        $primaryUrl = !empty($bookmark->progress_url) ? $bookmark->progress_url : $bookmark->url;

        // 2. Garantizar que las URLs alternativas sean un array válido
        $alternativeUrls = $bookmark->alternative_urls;
        if (is_string($alternativeUrls)) {
            $alternativeUrls = json_decode($alternativeUrls, true) ?? [];
        }
        if (!is_array($alternativeUrls)) {
            $alternativeUrls = [];
        }

        // 3. Ejecutar la comprobación para TODOS los links (Principal + Alternativos)
        $results = $checker->checkBookmarkLinks($primaryUrl, $alternativeUrls);

        // 4. Evaluación del resultado
        $hasNewChapter = collect($results)->contains('has_next', true);

        // 🔴 ESTA ERA LA LÍNEA FALTANTE: Guardar el nuevo estado en la base de datos
        $bookmark->update([
            'has_new_episode' => $hasNewChapter
        ]);

        return response()->json([
            'bookmark_id'     => $bookmark->id,
            'has_new_episode' => $hasNewChapter,
            'checks'          => $results
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error'   => 'Error al procesar la solicitud',
            'message' => $e->getMessage()
        ], 500);
    }
}



}
