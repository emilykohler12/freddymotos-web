<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MechanicJob;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MechanicJobController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        MechanicJob::create($this->validated($request));

        return redirect()->route('admin.workshop.index', ['tab' => 'mecanicos'])->with('status', 'Trabajo registrado.');
    }

    public function update(Request $request, MechanicJob $mechanicJob): RedirectResponse
    {
        $mechanicJob->update($this->validated($request));

        return redirect()->route('admin.workshop.index', ['tab' => 'mecanicos'])->with('status', 'Trabajo actualizado.');
    }

    public function togglePaid(MechanicJob $mechanicJob): RedirectResponse
    {
        $mechanicJob->update(['pagado' => ! $mechanicJob->pagado]);

        return redirect()->route('admin.workshop.index', ['tab' => 'mecanicos'])->with('status', $mechanicJob->pagado ? 'Trabajo marcado como pagado.' : 'Trabajo marcado como pendiente.');
    }

    public function destroy(MechanicJob $mechanicJob): RedirectResponse
    {
        $mechanicJob->delete();

        return redirect()->route('admin.workshop.index', ['tab' => 'mecanicos'])->with('status', 'Trabajo eliminado.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'mechanic_id' => ['required', 'exists:mechanics,id'],
            'moto' => ['required', 'string', 'max:150'],
            'problema' => ['required', 'string', 'max:1000'],
            'repuestos' => ['nullable', 'string', 'max:1000'],
            'monto_a_pagar' => ['required', 'numeric', 'min:0'],
        ]);
    }
}
