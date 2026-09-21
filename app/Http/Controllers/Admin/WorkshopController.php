<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mechanic;
use App\Models\WorkshopCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkshopController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.workshop.index', [
            'activeTab' => $request->string('tab', 'categorias')->toString(),
            'categories' => WorkshopCategory::orderBy('name')->get(),
            'mechanics' => Mechanic::with(['jobs' => fn ($q) => $q->latest()])->orderBy('name')->get(),
        ]);
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:255'],
            'price' => ['nullable', 'numeric', 'min:0'],
        ]);

        WorkshopCategory::create($data);

        return redirect()->route('admin.workshop.index', ['tab' => 'categorias'])->with('status', 'Categoría creada.');
    }

    public function destroyCategory(WorkshopCategory $workshopCategory): RedirectResponse
    {
        $workshopCategory->delete();

        return redirect()->route('admin.workshop.index', ['tab' => 'categorias'])->with('status', 'Categoría eliminada.');
    }
}
