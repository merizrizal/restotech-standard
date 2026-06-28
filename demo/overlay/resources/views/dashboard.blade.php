@extends('restotech-standard::back-office.layout')

@section('title', 'Restotech Demo')

@section('content')
    <section class="card stack">
        <h1>Restotech Standard Demo</h1>
        <p>Isolated Laravel demo app for the package interface and live POS/back-office flows.</p>

        @guest
            <div class="flash">
                <strong>Demo credentials:</strong>
                {{ $demoEmail }} / {{ $demoPassword }}
            </div>
        @else
            <div class="flash">
                Signed in as <strong>{{ $currentUser->email }}</strong>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Sign out</button>
            </form>
        @endguest
    </section>

    <section class="card stack">
        <h2>Launch paths</h2>
        <div class="actions">
            <a href="{{ route('restotech.standard.back_office.home') }}">Open Back Office</a>
            <a href="{{ route('restotech.standard.pos.shell') }}">Open POS shell</a>
            <a href="{{ route('login') }}">Login page</a>
        </div>
        <p>Seeded table ID for POS: <strong>{{ $demoTableId }}</strong></p>
    </section>

    <section class="card stack">
        <h2>Seeded demo data</h2>
        <table>
            <tbody>
                <tr>
                    <th>Open transaction day</th>
                    <td>{{ $openTransactionDay?->business_date ?? 'none' }}</td>
                </tr>
                <tr>
                    <th>Open cashier balance</th>
                    <td>#{{ $openCashierBalance?->id ?? 'none' }}</td>
                </tr>
                <tr>
                    <th>Dining areas</th>
                    <td>{{ $diningAreasCount }}</td>
                </tr>
                <tr>
                    <th>Dining tables</th>
                    <td>{{ $diningTablesCount }}</td>
                </tr>
                <tr>
                    <th>Menu items</th>
                    <td>{{ $menuItemsCount }}</td>
                </tr>
            </tbody>
        </table>
    </section>
@endsection
