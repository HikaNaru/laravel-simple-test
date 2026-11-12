@extends('layouts.app')

@section('title', 'Couriers')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Couriers</h1>
        <a href="{{ route('couriers.create') }}" class="btn btn-primary">New courier</a>
    </div>

    <form method="GET" action="{{ route('couriers.index') }}" id="filter-form" class="card card-body mb-3">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="search" class="form-label">Search</label>
                <input type="search" id="search" name="search" class="form-control"
                       value="{{ $filters['search'] ?? '' }}" placeholder="Name, code, phone or email">
            </div>
            <div class="col-md-3">
                <label for="sort_registered" class="form-label">Sort</label>
                <select id="sort_registered" name="sort_registered" class="form-select">
                    <option value="">Name</option>
                    <option value="desc" @selected(($filters['sort_registered'] ?? null) === 'desc')>Newest first</option>
                    <option value="asc" @selected(($filters['sort_registered'] ?? null) === 'asc')>Oldest first</option>
                </select>
            </div>
            <div class="col-md-5">
                <span class="form-label d-block">Level</span>
                <input type="hidden" name="level" id="level" value="{{ implode(',', $filters['level'] ?? []) }}">
                @foreach (range(App\Models\Courier::MIN_LEVEL, App\Models\Courier::MAX_LEVEL) as $level)
                    <div class="form-check form-check-inline">
                        <input type="checkbox" class="form-check-input level-checkbox" id="level-{{ $level }}"
                               value="{{ $level }}" @checked(in_array($level, $filters['level'] ?? []))>
                        <label class="form-check-label" for="level-{{ $level }}">{{ $level }}</label>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Apply</button>
            <a href="{{ route('couriers.index') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>

    <div class="table-responsive card">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Level</th>
                    <th>Status</th>
                    <th>Registered</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($couriers as $courier)
                    <tr>
                        <td>{{ $courier->code }}</td>
                        <td>{{ $courier->name }}</td>
                        <td>{{ $courier->phone }}</td>
                        <td>{{ $courier->level }}</td>
                        <td>{{ $courier->status->label() }}</td>
                        <td>{{ $courier->registered_at->format('Y-m-d') }}</td>
                        <td class="text-end">
                            <a href="{{ route('couriers.show', $courier) }}" class="btn btn-sm btn-outline-secondary">View</a>
                            <a href="{{ route('couriers.edit', $courier) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form method="POST" action="{{ route('couriers.destroy', $courier) }}" class="d-inline"
                                  onsubmit="return confirm('Delete this courier?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No couriers found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">{{ $couriers->links() }}</div>
@endsection

@push('scripts')
    <script>
        // Joins checked levels into a single "level" param, e.g. ?level=2,3
        document.getElementById('filter-form').addEventListener('submit', () => {
            const input = document.getElementById('level');
            input.value = [...document.querySelectorAll('.level-checkbox:checked')].map(c => c.value).join(',');
            input.disabled = input.value === '';
        });
    </script>
@endpush
