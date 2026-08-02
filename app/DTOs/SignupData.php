<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Enums\BusinessType;

final readonly class SignupData
{
    public function __construct(
        public string $businessName,
        public string $ownerName,
        public string $email,
        public string $password,
        public ?string $phone,
        public string $country,
        public BusinessType $businessType,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromValidated(array $data): self
    {
        return new self(
            businessName: trim((string) $data['business_name']),
            ownerName: trim((string) $data['owner_name']),
            email: strtolower(trim((string) $data['email'])),
            password: (string) $data['password'],
            phone: filled($data['phone'] ?? null) ? trim((string) $data['phone']) : null,
            country: strtoupper(substr(trim((string) ($data['country'] ?? 'GB')), 0, 2)),
            businessType: BusinessType::from((string) $data['business_type']),
        );
    }
}
