<?php

namespace App\Http\Controllers;

use App\Models\WorkshopCategory;
use App\Models\WorkshopInquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkshopController extends Controller
{
    /** Página pública /taller: categorías del taller + formulario de consulta. */
    public function index(): View
    {
        return view('workshop.index', [
            'categories' => WorkshopCategory::orderBy('name')->get(),
        ]);
    }

    /** Formulario de consultas generales del Home: no está vinculado a ninguna categoría. */
    public function storeInquiry(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:40', 'regex:/^[0-9+()\s-]{6,40}$/'],
            'email' => ['nullable', 'email', 'max:160'],
            'message' => ['required', 'string', 'max:1000'],
        ], [], [
            'name' => 'nombre',
            'phone' => 'teléfono',
            'message' => 'mensaje',
        ]);

        WorkshopInquiry::create($data);

        // Clave propia (no "status") para no duplicar el aviso flotante global del layout.
        return back()->with('consulta_status', '¡Gracias! Recibimos tu consulta, te vamos a contactar a la brevedad.');
    }
}
