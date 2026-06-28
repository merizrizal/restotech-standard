@extends('restotech-standard::back-office.layout')

@section('title', 'Restotech Demo Login')

@section('content')
    <section class="card stack">
        <h1>Restotech Demo Login</h1>
        <p>Sign in with the seeded demo user to explore the Back Office and POS screens.</p>

        @if ($errors->any())
            <div class="errors">
                <strong>Login failed.</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form class="stack" method="POST" action="{{ route('login.store') }}">
            @csrf

            <div class="field">
                <label for="email">Email</label>
                <input id="email" type="text" name="email" value="{{ old('email', config('restotech-standard.demo.user_email', 'demo@restotech.test')) }}" autocomplete="email">
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" value="{{ old('password', config('restotech-standard.demo.user_password', 'password')) }}" autocomplete="current-password">
            </div>

            <div class="field">
                <label>
                    <input type="checkbox" name="remember" value="1">
                    Remember me
                </label>
            </div>

            <button type="submit">Sign in</button>
        </form>

        <p>
            Demo credentials:
            <strong>{{ config('restotech-standard.demo.user_email', 'demo@restotech.test') }}</strong>
            /
            <strong>{{ config('restotech-standard.demo.user_password', 'password') }}</strong>
        </p>
    </section>
@endsection
