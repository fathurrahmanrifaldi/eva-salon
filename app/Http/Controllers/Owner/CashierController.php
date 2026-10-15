<?php

namespace App\Http\Controllers\Owner;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\CashierRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

/** Akun kasir (AK-01, AK-02). Password sementara acak, wajib diganti saat login pertama. */
class CashierController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', User::class);

        return view('owner.cashiers.index', ['cashiers' => User::where('role', Role::Kasir->value)->orderBy('name')->get()]);
    }

    public function store(CashierRequest $request): RedirectResponse
    {
        Gate::authorize('create', User::class);

        $password = $this->tempPassword();
        $user = User::create($request->validated() + [
            'role' => Role::Kasir, 'password' => $password, 'must_change_password' => true, 'is_active' => true,
        ]);

        return back()->with('success', "Akun {$user->name} dibuat.")->with('temp_password', [$user->email, $password]);
    }

    public function update(CashierRequest $request, User $cashier): RedirectResponse
    {
        Gate::authorize('update', $cashier);
        abort_unless($cashier->role === Role::Kasir, 404);

        $cashier->update($request->validated());

        return back()->with('success', 'Akun diperbarui.');
    }

    public function toggle(User $cashier): RedirectResponse
    {
        Gate::authorize('update', $cashier);
        abort_unless($cashier->role === Role::Kasir, 404);

        $cashier->update(['is_active' => ! $cashier->is_active]);

        return back()->with('success', $cashier->is_active ? 'Akun diaktifkan.' : 'Akun dinonaktifkan.');
    }

    public function resetPassword(User $cashier): RedirectResponse
    {
        Gate::authorize('update', $cashier);
        abort_unless($cashier->role === Role::Kasir, 404);

        $password = $this->tempPassword();
        $cashier->update(['password' => $password, 'must_change_password' => true]);

        return back()->with('success', 'Password direset.')->with('temp_password', [$cashier->email, $password]);
    }

    private function tempPassword(): string
    {
        return Str::password(12, symbols: false);
    }
}
