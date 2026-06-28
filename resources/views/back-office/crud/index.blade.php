@extends('restotech-standard::back-office.layout')

@section('title', $resource->title())

@section('content')
    <section class="card stack">
        <div class="actions" style="display:flex; justify-content:space-between; align-items:center;">
            <h1>{{ $resource->title() }}</h1>
            <a href="{{ route($resource->route('create')) }}">Create {{ $resource->singularLabel() }}</a>
        </div>

        <table>
            <thead>
                <tr>
                    @foreach ($columns as $column)
                        <th>{{ $column['label'] }}</th>
                    @endforeach
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr>
                        @foreach ($columns as $column)
                            @php($value = data_get($item, $column['name']))
                            <td>{{ $column['name'] === 'is_active' ? ($value ? 'Yes' : 'No') : $value }}</td>
                        @endforeach
                        <td class="actions">
                            <a href="{{ route($resource->route('edit'), $item->getKey()) }}">Edit</a>
                            <form
                                method="POST"
                                action="{{ route($resource->route('destroy'), $item->getKey()) }}"
                                style="display:inline"
                                onsubmit="return confirm('Delete this {{ $resource->singularLabel() }}?')"
                            >
                                @csrf
                                @method('DELETE')
                                <button type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columns) + 1 }}">No records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>
@endsection
