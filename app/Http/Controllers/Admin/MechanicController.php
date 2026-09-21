<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mechanic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MechanicController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:40', 'regex:/^[0-9+()\s-]{6,40}$/'],
            'email' => ['nullable', 'email', 'max:160'],
        ]);

        Mechanic::create($data);

        return redirect()->route('admin.workshop.index', ['tab' => 'mecanicos'])->with('status', 'Mecánico añadido.');
    }

    public function destroy(Mechanic $mechanic): RedirectResponse
    {
        $mechanic->delete();

        return redirect()->route('admin.workshop.index', ['tab' => 'mecanicos'])->with('status', 'Mecánico eliminado.');
    }
}
