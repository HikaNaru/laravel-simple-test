@extends('layouts.app')

@section('title', $courier->name)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">{{ $courier->name }}</h1>
        <div>
            <a href="{{ route('couriers.edit', $courier) }}" class="btn btn-primary">Edit</a>
            <a href="{{ route('couriers.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
    </div>

    <dl class="row card card-body">
        <dt class="col-sm-3">Code</dt><dd class="col-sm-9">{{ $courier->code }}</dd>
        <dt class="col-sm-3">Name</dt><dd class="col-sm-9">{{ $courier->name }}</dd>
        <dt class="col-sm-3">Email</dt><dd class="col-sm-9">{{ $courier->email }}</dd>
        <dt class="col-sm-3">Phone</dt><dd class="col-sm-9">{{ $courier->phone }}</dd>
        <dt class="col-sm-3">National ID</dt><dd class="col-sm-9">{{ $courier->national_id }}</dd>
        <dt class="col-sm-3">Address</dt><dd class="col-sm-9">{{ $courier->address }}</dd>
        <dt class="col-sm-3">City</dt><dd class="col-sm-9">{{ $courier->city }}</dd>
        <dt class="col-sm-3">Vehicle type</dt><dd class="col-sm-9">{{ $courier->vehicle_type->label() }}</dd>
        <dt class="col-sm-3">Plate number</dt><dd class="col-sm-9">{{ $courier->plate_number ?? '-' }}</dd>
        <dt class="col-sm-3">Level</dt><dd class="col-sm-9">{{ $courier->level }}</dd>
        <dt class="col-sm-3">Status</dt><dd class="col-sm-9">{{ $courier->status->label() }}</dd>
        <dt class="col-sm-3">Registered at</dt><dd class="col-sm-9">{{ $courier->registered_at->format('Y-m-d') }}</dd>
        <dt class="col-sm-3">Created at</dt><dd class="col-sm-9">{{ $courier->created_at }}</dd>
        <dt class="col-sm-3">Updated at</dt><dd class="col-sm-9">{{ $courier->updated_at }}</dd>
    </dl>
@endsection
