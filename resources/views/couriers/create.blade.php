@extends('layouts.app')

@section('title', 'New courier')

@section('content')
    <h1 class="h3 mb-3">New courier</h1>

    <form method="POST" action="{{ route('couriers.store') }}" class="card card-body">
        @include('couriers._form', ['submitLabel' => 'Create'])
    </form>
@endsection
