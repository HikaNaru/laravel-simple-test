<?php

namespace App\Models;

use App\Enums\CourierStatus;
use App\Enums\VehicleType;
use Database\Factories\CourierFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Courier extends Model
{
    /** @use HasFactory<CourierFactory> */
    use HasFactory;

    public const MIN_LEVEL = 1;

    public const MAX_LEVEL = 5;

    protected $fillable = [
        'code',
        'name',
        'email',
        'phone',
        'national_id',
        'address',
        'city',
        'vehicle_type',
        'plate_number',
        'level',
        'status',
        'registered_at',
    ];

    protected function casts(): array
    {
        return [
            'vehicle_type' => VehicleType::class,
            'status' => CourierStatus::class,
            'level' => 'integer',
            'registered_at' => 'date',
        ];
    }

    /**
     * Every word in the keyword must match name, code, phone or email.
     * Usage: Courier::search('budi agung') finds "Budiono Hadi Agung".
     */
    #[Scope]
    protected function search(Builder $query, ?string $keyword): void
    {
        $words = preg_split('/\s+/', trim((string) $keyword), -1, PREG_SPLIT_NO_EMPTY);

        foreach ($words as $word) {
            $query->where(fn (Builder $q) => $q
                ->where('name', 'like', "%{$word}%")
                ->orWhere('code', 'like', "%{$word}%")
                ->orWhere('phone', 'like', "%{$word}%")
                ->orWhere('email', 'like', "%{$word}%"));
        }
    }

    /** Keeps couriers having any of the given levels. Usage: filterByLevels([2, 4]). */
    #[Scope]
    protected function filterByLevels(Builder $query, array $levels): void
    {
        if ($levels !== []) {
            $query->whereIn('level', $levels);
        }
    }

    /** Orders by registration date. Usage: orderByRegisteredAt('desc'); other values are ignored. */
    #[Scope]
    protected function orderByRegisteredAt(Builder $query, ?string $direction): void
    {
        if (in_array($direction, ['asc', 'desc'], true)) {
            $query->orderBy('registered_at', $direction);
        }
    }
}
