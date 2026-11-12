<?php

namespace App\Http\Requests;

use App\Models\Courier;
use Illuminate\Foundation\Http\FormRequest;

class IndexCourierRequest extends FormRequest
{
    /** Turns the comma-separated "level" query (?level=2,3) into an array. */
    protected function prepareForValidation(): void
    {
        if (is_string($this->query('level'))) {
            $this->merge(['level' => array_values(array_filter(explode(',', $this->query('level')), 'strlen'))]);
        }
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'level' => ['nullable', 'array'],
            'level.*' => ['integer', 'between:'.Courier::MIN_LEVEL.','.Courier::MAX_LEVEL],
            'sort_registered' => ['nullable', 'in:asc,desc'],
        ];
    }
}
