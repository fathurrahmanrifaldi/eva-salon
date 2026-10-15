<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use App\Support\Phone;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Customer::class);

        $q = trim((string) $request->query('q'));
        $customers = Customer::query()
            ->when($q !== '', fn ($query) => $query->where(fn ($w) => $w
                ->where('name', 'like', "%$q%")
                ->orWhere('phone', 'like', '%'.(Phone::normalize($q) ?? preg_replace('/\D/', '', $q) ?: $q).'%')))
            ->withCount(['bookings as visits' => fn ($b) => $b->where('status', 'completed')])
            ->orderBy('name')->paginate(25)->withQueryString();

        return view('admin.customers.index', compact('customers', 'q'));
    }

    public function show(Customer $customer): View
    {
        Gate::authorize('view', $customer);

        $bookings = $customer->bookings()->with(['items', 'staff', 'payments'])->orderByDesc('start_at')->get();

        return view('admin.customers.show', [
            'customer' => $customer,
            'bookings' => $bookings,
            'totalPaid' => $bookings->sum(fn ($b) => $b->paidTotal()),
        ]);
    }

    public function update(CustomerRequest $request, Customer $customer): RedirectResponse
    {
        Gate::authorize('update', $customer);

        $customer->fill($request->validated());
        // shortcut: perubahan dicatat ke log aplikasi, belum ada tabel audit; tambahkan jika owner butuh riwayat di UI.
        Log::info('customer.updated', ['customer' => $customer->id, 'by' => $request->user()->id, 'changes' => $customer->getDirty()]);
        $customer->save();

        return back()->with('success', 'Data pelanggan diperbarui.');
    }

    /** Saran pelanggan lama saat walk-in (WI-03): cari lewat telepon atau nama. */
    public function lookup(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Customer::class);

        $q = trim((string) $request->query('q'));
        if (mb_strlen($q) < 3) {
            return response()->json([]);
        }

        $digits = preg_replace('/\D/', '', $q);
        $phoneLike = Phone::normalize($q) ?? ($digits !== '' ? $digits : null);

        return response()->json(Customer::query()
            ->where(fn ($w) => $w->when($phoneLike, fn ($x) => $x->orWhere('phone', 'like', "%$phoneLike%"))->orWhere('name', 'like', "%$q%"))
            ->withCount(['bookings as visits' => fn ($b) => $b->where('status', 'completed')])
            ->limit(5)->get(['id', 'name', 'phone'])
            ->map(fn ($c) => ['name' => $c->name, 'phone' => $c->phone, 'visits' => $c->visits]));
    }
}
