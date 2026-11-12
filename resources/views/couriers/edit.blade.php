@extends('layouts.app')

@section('title', 'Edit '.$courier->name)

@section('content')
    <h1 class="h3 mb-3">Edit courier</h1>

    <form method="POST" action="{{ route('couriers.update', $courier) }}" class="card card-body">
        @method('PUT')
        @include('couriers._form', ['submitLabel' => 'Save'])
    </form>
@endsection
