@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">Dashboard</h1>
    
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> 
        Hoş geldiniz, <strong>{{ auth()->user()->name }}</strong>!
        @if(auth()->user()->role === 'admin')
            <span class="badge bg-warning text-dark ms-2">Admin</span>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Sistem Bilgisi</h5>
            <p class="card-text">Dashboard sayfası çalışıyor. Sol menüden diğer sayfalara gidebilirsiniz.</p>
        </div>
    </div>
</div>
@endsection