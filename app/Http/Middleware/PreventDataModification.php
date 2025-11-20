<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventDataModification
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Lütfen giriş yapın.');
        }

        // Admin ise tüm işlemlere izin ver
        if (auth()->user()->role === 'admin') {
            return $next($request);
        }

        // Normal kullanıcı update, delete veya export işlemi yapmaya çalışıyorsa engelle
        $forbiddenActions = ['update', 'edit', 'destroy', 'delete', 'export'];
        
        if ($request->routeIs('*.update') || 
            $request->routeIs('*.edit') || 
            $request->routeIs('*.destroy') || 
            $request->routeIs('*.delete') || 
            $request->routeIs('*.export')) {
            abort(403, 'Bu işlem için yetkiniz yok. Sadece veri girişi yapabilirsiniz.');
        }

        return $next($request);
    }
}