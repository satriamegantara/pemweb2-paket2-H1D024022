<?php

namespace App\Http\Middleware;

use App\Models\Cart;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class EnsureCartExists
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            $request->merge(['cart' => $this->getOrCreateUserCart($request->user()->id)]);
        } else {
            $guestId = $this->getGuestId($request);
            $request->merge(['cart' => $this->getOrCreateGuestCart($guestId)]);
        }

        return $next($request);
    }

    private function getOrCreateUserCart(int $userId): Cart
    {
        return Cart::firstOrCreate(
            ['user_id' => $userId],
            ['guest_id' => null]
        );
    }

    private function getOrCreateGuestCart(string $guestId): Cart
    {
        $cart = Cart::forGuest($guestId)->first();

        if (!$cart) {
            $cart = Cart::create(['guest_id' => $guestId, 'user_id' => null]);
        }

        return $cart;
    }

    private function getGuestId(Request $request): string
    {
        $guestId = $request->session()->get('guest_id');

        if (!$guestId) {
            $guestId = Str::uuid()->toString();
            $request->session()->put('guest_id', $guestId);
        }

        return $guestId;
    }
}
