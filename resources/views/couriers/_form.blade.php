@csrf

@php
    $fields = [
        'code' => ['Code', 'text'],
        'name' => ['Name', 'text'],
        'email' => ['Email', 'email'],
        'phone' => ['Phone', 'text'],
        'national_id' => ['National ID', 'text'],
        'city' => ['City', 'text'],
        'plate_number' => ['Plate number', 'text'],
        'registered_at' => ['Registered at', 'date'],
    ];
@endphp

<div class="row g-3">
    @foreach ($fields as $name => [$label, $type])
        <div class="col-md-6">
            <label for="{{ $name }}" class="form-label">{{ $label }}</label>
            <input type="{{ $type }}" id="{{ $name }}" name="{{ $name }}"
                   value="{{ old($name, $courier->$name instanceof DateTimeInterface ? $courier->$name->format('Y-m-d') : $courier->$name) }}"
                   class="form-control @error($name) is-invalid @enderror">
            @error($name)<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    @endforeach

    <div class="col-12">
        <label for="address" class="form-label">Address</label>
        <textarea id="address" name="address" rows="2"
                  class="form-control @error('address') is-invalid @enderror">{{ old('address', $courier->address) }}</textarea>
        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label for="vehicle_type" class="form-label">Vehicle type</label>
        <select id="vehicle_type" name="vehicle_type" class="form-select @error('vehicle_type') is-invalid @enderror">
            <option value="">Select...</option>
            @foreach ($vehicleTypes as $type)
                <option value="{{ $type->value }}" @selected(old('vehicle_type', $courier->vehicle_type?->value) === $type->value)>
                    {{ $type->label() }}
                </option>
            @endforeach
        </select>
        @error('vehicle_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label for="level" class="form-label">Level</label>
        <select id="level" name="level" class="form-select @error('level') is-invalid @enderror">
            @foreach ($levels as $level)
                <option value="{{ $level }}" @selected((int) old('level', $courier->level) === $level)>{{ $level }}</option>
            @endforeach
        </select>
        @error('level')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label for="status" class="form-label">Status</label>
        <select id="status" name="status" class="form-select @error('status') is-invalid @enderror">
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(old('status', $courier->status?->value) === $status->value)>
                    {{ $status->label() }}
                </option>
            @endforeach
        </select>
        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="mt-4">
    <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
    <a href="{{ route('couriers.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>
