<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingCompany;
use App\Models\ShippingZone;
use App\Support\Sorting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShippingController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $sort = $request->query('sort', 'name_asc');

        $sortByPrice = str_starts_with($sort, 'price');
        $sortDirection = str_ends_with($sort, 'desc') ? 'desc' : 'asc';

        $applySort = fn ($q) => $sortByPrice
            ? $q->orderBy('price', $sortDirection)
            : $q->orderByRaw(Sorting::foldedName('name') . ' ' . strtoupper($sortDirection));

        return view('admin.shipping.index', [
            'zones' => $applySort(ShippingZone::with('company')
                ->when($search !== '', fn ($q) => $q->whereRaw(Sorting::foldedName('name') . ' LIKE ?', ['%' . Sorting::fold($search) . '%']))
            )->get(),
            'companies' => $applySort(ShippingCompany::query()
                ->when($search !== '', fn ($q) => $q->whereRaw(Sorting::foldedName('name') . ' LIKE ?', ['%' . Sorting::fold($search) . '%']))
            )->get(),
            'search' => $search,
            'sort' => $sort,
        ]);
    }

    /* ---------------- Empresas de envío ---------------- */

    public function storeCompany(Request $request): RedirectResponse
    {
        $data = $this->validatedCompany($request);
        $data['active'] = $request->boolean('active', true);

        ShippingCompany::create($data);

        return back()->with('status', 'Empresa de envío creada.');
    }

    public function updateCompany(Request $request, ShippingCompany $company): RedirectResponse
    {
        $data = $this->validatedCompany($request);
        $data['active'] = $request->boolean('active', true);

        $company->update($data);

        return back()->with('status', 'Empresa de envío actualizada.');
    }

    public function destroyCompany(ShippingCompany $company): RedirectResponse
    {
        $company->delete();

        return back()->with('status', 'Empresa eliminada.');
    }

    /* ---------------- Zonas de envío ---------------- */

    public function storeZone(Request $request): RedirectResponse
    {
        ShippingZone::create($this->validatedZone($request));

        return back()->with('status', 'Zona de envío creada.');
    }

    public function updateZone(Request $request, ShippingZone $zone): RedirectResponse
    {
        $zone->update($this->validatedZone($request));

        return back()->with('status', 'Zona de envío actualizada.');
    }

    public function destroyZone(ShippingZone $zone): RedirectResponse
    {
        $zone->delete();

        return back()->with('status', 'Zona eliminada.');
    }

    private function validatedCompany(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'contact_name' => ['nullable', 'string', 'max:120'],
            'contact_phone' => ['nullable', 'string', 'max:40', 'regex:/^[0-9+()\s-]{6,40}$/'],
            'contact_email' => ['nullable', 'email', 'max:160'],
            'service_type' => ['nullable', 'string', 'max:120'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);
    }

    private function validatedZone(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'provincia' => ['nullable', 'string', 'max:120'],
            'localidad' => ['nullable', 'string', 'max:120'],
            'postal_code' => ['nullable', 'string', 'max:40'],
            'price' => ['required', 'numeric', 'min:0'],
            'shipping_company_id' => ['nullable', 'exists:shipping_companies,id'],
        ]);
    }
}
