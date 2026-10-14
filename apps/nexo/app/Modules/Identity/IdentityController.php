<?php

namespace App\Modules\Identity;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IdentityController
{
    public function csrf()
    {
        return response()->noContent();
    }

    public function login(Request $r)
    {
        $data = $r->validate(['login' => 'required|email', 'password' => 'required|string']);
        abort_unless(Auth::attempt(['email' => $data['login'], 'password' => $data['password']]), 401, 'Credenciales incorrectas.');
        $r->session()->regenerate();

        return $this->me($r);
    }

    public function logout(Request $r)
    {
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();

        return response()->noContent();
    }

    public function me(Request $r)
    {
        return response()->json(['id' => (string) $r->user()->id, 'organizationId' => $r->user()->organization_id, 'role' => $r->user()->role]);
    }
}
