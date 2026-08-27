<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(): View
    {
        $payments = Payment::query()
            ->whereHas('roomBilling.roomAssignment', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->with([
                'roomBilling.roomPayable.payable',
                'roomBilling.roomAssignment.room',
            ])
            ->latest('payment_date')
            ->latest('id')
            ->paginate(15);

        return view('tenant.payments.index', compact('payments'));
    }
}
