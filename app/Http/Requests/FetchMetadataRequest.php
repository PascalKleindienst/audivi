<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\DataProviderType;
use App\Facades\DataProvider;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;

final class FetchMetadataRequest extends FormRequest
{
    /**
     * @return array<string, list<In|string>>
     */
    public function rules(): array
    {
        return [
            'query' => ['required', 'string'],
            'type' => ['required', 'string', Rule::in(DataProviderType::cases())],
            'provider' => ['required', 'string', Rule::in(DataProvider::providers())],
            'locale' => ['string'],
        ];
    }
}
