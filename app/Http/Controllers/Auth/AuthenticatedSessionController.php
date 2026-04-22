<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();

        $this->mergeGuestCart($request, $user->id);

        $default = match ($user?->role) {
            'admin' => route('admin.dashboard', absolute: false),
            'penjual' => route('seller.dashboard', absolute: false),
            default => route('dashboard', absolute: false),
        };

        return redirect()->intended($default);
    }

    private function mergeGuestCart(Request $request, int $userId): void
    {
        $guestId = $request->session()->get('guest_id');

        if (!$guestId) {
            return;
        }

        $userCart = Cart::firstOrCreate(
            ['user_id' => $userId],
            ['guest_id' => null]
        );

        $userCart->mergeFromGuest($guestId);

        $request->session()->forget('guest_id');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
