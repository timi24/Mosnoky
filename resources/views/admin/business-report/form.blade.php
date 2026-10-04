@extends('layouts.admin')

@section('title', 'Rapport mensuel')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Rapport mensuel de gestion</h1>
            <p class="page-subtitle">Chiffre d'affaires, commandes, produits les plus vendus et stock — au format PDF.</p>
        </div>
    </div>

    <div class="form-panel">
        <form method="GET" action="{{ route('admin.business-report.download') }}" target="_blank">
            <div class="field">
                <label for="month">Mois</label>
                <select id="month" name="month">
                    @foreach (['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'] as $i => $label)
                        <option value="{{ $i + 1 }}" {{ $month == $i + 1 ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label for="year">Année</label>
                <select id="year" name="year">
                    @for ($y = now()->year; $y >= now()->year - 3; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="button primary">📄 Télécharger le rapport PDF</button>
            </div>
        </form>
    </div>
@endsection
