<?php

namespace App\Http\Controllers;

use App\Enums\CourierStatus;
use App\Enums\VehicleType;
use App\Http\Requests\CourierRequest;
use App\Http\Requests\IndexCourierRequest;
use App\Models\Courier;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CourierController extends Controller
{
    private const PER_PAGE = 10;

    public function index(IndexCourierRequest $request): View
    {
        $filters = $request->validated();

        $couriers = Courier::query()
            ->search($filters['search'] ?? null)
            ->filterByLevels($filters['level'] ?? [])
            ->orderByRegisteredAt($filters['sort_registered'] ?? null)
            ->orderBy('name')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        return view('couriers.index', compact('couriers', 'filters'));
    }

    public function create(): View
    {
        return view('couriers.create', [
            'courier' => new Courier(['level' => Courier::MIN_LEVEL, 'status' => CourierStatus::Active]),
            ...$this->formOptions(),
        ]);
    }

    public function store(CourierRequest $request): RedirectResponse
    {
        $courier = Courier::create($request->validated());

        return redirect()->route('couriers.show', $courier)->with('status', 'Courier created.');
    }

    public function show(Courier $courier): View
    {
        return view('couriers.show', compact('courier'));
    }

    public function edit(Courier $courier): View
    {
        return view('couriers.edit', ['courier' => $courier, ...$this->formOptions()]);
    }

    public function update(CourierRequest $request, Courier $courier): RedirectResponse
    {
        $courier->update($request->validated());

        return redirect()->route('couriers.show', $courier)->with('status', 'Courier updated.');
    }

    public function destroy(Courier $courier): RedirectResponse
    {
        $courier->delete();

        return redirect()->route('couriers.index')->with('status', 'Courier deleted.');
    }

    private function formOptions(): array
    {
        return [
            'vehicleTypes' => VehicleType::cases(),
            'statuses' => CourierStatus::cases(),
            'levels' => range(Courier::MIN_LEVEL, Courier::MAX_LEVEL),
        ];
    }
}
