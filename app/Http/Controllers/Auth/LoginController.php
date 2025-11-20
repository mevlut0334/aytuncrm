<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     */
    protected $redirectTo = '/dashboard';  // ✅ Dashboard'a yönlendir

    /**
     * ✅ Constructor'ı BOŞ bırak - middleware'i route'ta hallederiz
     */
    public function __construct()
    {
        // Boş - middleware'leri route dosyasında tanımladık
    }

    /**
     * Login sonrası yönlendirme
     */
    protected function redirectTo()
    {
        return route('dashboard');  // ✅ Hem admin hem normal kullanıcı dashboard'a gitsin
    }

    /**
     * Logout sonrası yönlendirme
     */
    protected function loggedOut(\Illuminate\Http\Request $request)
    {
        return redirect('/login');
    }
}