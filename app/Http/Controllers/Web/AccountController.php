<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Datos editables de la propia cuenta desde el panel (por ahora solo el nombre).
 */
class AccountController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
        ], [
            'name.required' => 'Escribe tu nombre.',
            'name.min' => 'El nombre debe tener al menos 2 caracteres.',
        ]);

        $request->user()->update(['name' => trim($validated['name'])]);

        return back()->with('success', 'Nombre actualizado correctamente.');
    }
}
