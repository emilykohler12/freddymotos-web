<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ShippingCompany;
use App\Models\ShippingZone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShippingController extends Controller
{
    public function index(): View
    {
        return view('admin.shipping.index', [
            'zones' => ShippingZone::with('company')->orderBy('name')->get(),
            'companies' => ShippingCompany::orderBy('name')->get(),
        ]);
    }

    /* ---------------- Empresas de envío ---------------- */

    public function storeCompany(Request $request): RedirectResponse
    {
        $data = $this->validatedCompany($request);
        $data['active'] = $request->boolean('active', true);

        $company = ShippingCompany::create($data);

        ActivityLog::log('envios', "Empresa de envío creada: {$company->name}", $company);

        return back()->with('status', 'Empresa de envío creada.');
    }

    public function updateCompany(Request $request, ShippingCompany $company): RedirectResponse
    {
        $data = $this->validatedCompany($request);
        $data['active'] = $request->boolean('active', true);

        $company->update($data);

        ActivityLog::log('envios', "Empresa de envío editada: {$company->name}", $company);

        return back()->with('status', 'Empresa de envío actualizada.');
    }

    public function destroyCompany(ShippingCompany $company): RedirectResponse
    {
        $name = $company->name;
        $company->delete();

        ActivityLog::log('envios', "Empresa de envío eliminada: {$name}");

        return back()->with('status', 'Empresa eliminada.');
    }

    /* ---------------- Zonas de envío ---------------- */

    public function storeZone(Request $request): RedirectResponse
    {
        $zone = ShippingZone::create($this->validatedZone($request));

        ActivityLog::log('envios', "Zona de envío creada: {$zone->name}", $zone);

        return back()->with('status', 'Zona de envío creada.');
    }

    public function updateZone(Request $request, ShippingZone $zone): RedirectResponse
    {
        $zone->update($this->validatedZone($request));

        ActivityLog::log('envios', "Zona de envío editada: {$zone->name}", $zone);

        return back()->with('status', 'Zona de envío actualizada.');
    }

    public function destroyZone(ShippingZone $zone): RedirectResponse
    {
        $name = $zone->name;
        $zone->delete();

        ActivityLog::log('envios', "Zona de envío eliminada: {$name}");

        return back()->with('status', 'Zona eliminada.');
    }

    private function validatedCompany(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'contact_name' => ['nullable', 'string', 'max:120'],
            'contact_phone' => ['nullable', 'string', 'max:40'],
            'contact_email' => ['nullable', 'email', 'max:160'],
            'service_type' => ['nullable', 'string', 'max:120'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);
    }

    private function validatedZone(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'locations' => ['nullable', 'string', 'max:1000'],
            'postal_code' => ['nullable', 'string', 'max:40'],
            'price' => ['required', 'numeric', 'min:0'],
            'shipping_company_id' => ['nullable', 'exists:shipping_companies,id'],
        ]);
    }
}
