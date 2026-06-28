@extends('restotech-standard::back-office.layout')

@section('title', $heading)

@section('content')
    <section class="card stack">
        <h1>{{ $heading }}</h1>

        @if ($errors->any())
            <div class="errors">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ $formAction }}">
            @csrf
            @if ($formMethod !== 'POST')
                @method($formMethod)
            @endif

            @foreach ($fields as $field)
                @php($value = old($field['name'], data_get($model, $field['name'])))
                <div class="field">
                    <label for="{{ $field['name'] }}">{{ $field['label'] }}</label>

                    @if ($field['type'] === 'textarea')
                        <textarea
                            id="{{ $field['name'] }}"
                            name="{{ $field['name'] }}"
                            @if ($field['required'] ?? false) required @endif
                        >{{ $value }}</textarea>
                    @elseif ($field['type'] === 'checkbox')
                        <input type="hidden" name="{{ $field['name'] }}" value="0">
                        <label>
                            <input
                                type="checkbox"
                                id="{{ $field['name'] }}"
                                name="{{ $field['name'] }}"
                                value="1"
                                @checked((bool) $value)
                            >
                            {{ $field['label'] }}
                        </label>
                    @else
                        <input
                            type="{{ $field['type'] }}"
                            id="{{ $field['name'] }}"
                            name="{{ $field['name'] }}"
                            value="{{ $value }}"
                            @if ($field['required'] ?? false) required @endif
                        >
                    @endif

                    @error($field['name'])
                        <small>{{ $message }}</small>
                    @enderror
                </div>
            @endforeach

            <button type="submit">Save</button>
        </form>
    </section>
@endsection
