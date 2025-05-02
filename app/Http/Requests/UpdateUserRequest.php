<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Actions\Fortify\PasswordValidationRules;
use App\Models\User;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

final class UpdateUserRequest extends FormRequest
{
    use PasswordValidationRules;

    /**
     * @return array<string, array<int, Rule|string>>
     */
    public function rules(): array
    {
        /** @var User|null $user */
        $user = $this->route('user');

        return [
            'name' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user?->id],
            'is_admin' => ['required', 'boolean'],
            'password' => ['sometimes', ...$this->passwordRules()],
        ];
    }

    public function authorize(): bool
    {
        return auth()->user()?->can('admin') ?? false;
    }
}
