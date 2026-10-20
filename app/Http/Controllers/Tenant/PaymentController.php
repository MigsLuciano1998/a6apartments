<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Support\LiveTable;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $search = LiveTable::search($request);

        $payments = LiveTable::paginate(
            Payment::query()
                ->whereHas('roomBilling.roomAssignment', function ($query) {
                    $query->where('user_id', auth()->id());
                })
                ->with([
                    'roomBilling.roomPayable.payable',
                    'roomBilling.roomAssignment.room',
                ])
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('payment_method', 'like', "%{$search}%")
                            ->orWhere('reference_number', 'like', "%{$search}%")
                            ->orWhere('remarks', 'like', "%{$search}%")
                            ->orWhere('amount', 'like', "%{$search}%")
                            ->orWhereHas('roomBilling.roomPayable.payable', fn ($query) => $query->where('title', 'like', "%{$search}%"))
                            ->orWhereHas('roomBilling.roomAssignment.room', function ($query) use ($search) {
                                $query->where('room_number', 'like', "%{$search}%")
                                    ->orWhere('room_name', 'like', "%{$search}%");
                            });
                    });
                })
                ->latest('payment_date')
                ->latest('id'),
            $request,
        );

        if ($request->ajax()) {
            return view('tenant.payments.results', compact('payments', 'search'));
        }

        return view('tenant.payments.index', compact('payments', 'search'));
    }
}
