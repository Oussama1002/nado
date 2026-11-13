@extends('admin.layouts.app')

@section('title', 'Auto Enchère Navigation')

@section('content')
<div class="page-heading">
    <h3>Auto Enchère Dashboard</h3>
</div>

<div class="page-content">
    <div class="row g-4">

        <!-- Auto Enchère Section -->
        <div class="col-md-6">
            <div class="card shadow-sm p-4 text-center">
                <i class="bi bi-coin fs-1 text-warning mb-3"></i>
                <h5>Auto Enchère</h5>
                <p class="text-muted">Gérez toutes les enchères actives.</p>
                <a href="{{ route('admin.nadoautoenchere.index') }}" class="btn btn-primary btn-sm">Accéder</a>
            </div>
        </div>

        <!-- Offres Auto Enchère Section -->
        <div class="col-md-6">
            <div class="card shadow-sm p-4 text-center">
                <i class="bi bi-tags fs-1 text-success mb-3"></i>
                <h5>Offres Auto Enchère</h5>
                <p class="text-muted">Consultez les offres et enchères soumises.</p>
                <a href="{{ route('admin.nadoautoencherebid.index') }}" class="btn btn-success btn-sm">Accéder</a>
            </div>
        </div>

        <!-- Demandes de vente (vendeurs) -->
        <div class="col-md-6">
            <div class="card shadow-sm p-4 text-center">
                <i class="bi bi-truck fs-1 text-primary mb-3"></i>
                <h5>Demandes de vente</h5>
                <p class="text-muted">Véhicules soumis par les vendeurs (« Soumettre mes véhicules »).</p>
                <a href="{{ route('admin.nadoautoenchere.sellRequests') }}" class="btn btn-primary btn-sm">Accéder</a>
            </div>
        </div>

    </div>
</div>

@endsection
