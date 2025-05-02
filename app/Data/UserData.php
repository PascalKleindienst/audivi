<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\User;
use DateTime;
use Spatie\LaravelData\Data;

final class UserData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly string $avatar,
        public readonly bool $is_admin,
        public readonly ?DateTime $created_at,
        public readonly ?DateTime $updated_at
    ) {}

    public static function fromModel(User $user): self
    {
        return new self(
            $user->id,
            $user->name,
            $user->email,
            $user->profile_photo_url,
            $user->is_admin,
            $user->created_at,
            $user->updated_at,
        );
    }
}
