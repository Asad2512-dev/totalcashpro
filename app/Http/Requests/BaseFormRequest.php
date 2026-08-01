<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Base form request for TotalCashPro validation classes.
 *
 * Domain-specific requests should extend this class to keep
 * authorization and validation conventions consistent.
 */
abstract class BaseFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    abstract public function rules(): array;
}
