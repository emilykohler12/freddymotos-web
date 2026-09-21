<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkshopInquiry;
use Illuminate\Http\RedirectResponse;

class InquiryController extends Controller
{
    public function toggle(WorkshopInquiry $inquiry): RedirectResponse
    {
        $inquiry->update([
            'status' => $inquiry->status === WorkshopInquiry::STATUS_ATENDIDA
                ? WorkshopInquiry::STATUS_NUEVA
                : WorkshopInquiry::STATUS_ATENDIDA,
        ]);

        return redirect()->route('admin.activity.index', ['tab' => 'consultas'])->with('status', 'Consulta actualizada.');
    }

    public function destroy(WorkshopInquiry $inquiry): RedirectResponse
    {
        $inquiry->delete();

        return redirect()->route('admin.activity.index', ['tab' => 'consultas'])->with('status', 'Consulta eliminada.');
    }
}
