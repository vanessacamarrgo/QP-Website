<?php

declare(strict_types=1);

namespace App\Models;

final class BusCompany
{
    public function __construct(
        public int $id,
        public string $name,
        public string $url,
        public string $city,
        public string $status,
        public ?string $logo,
        public string $createdAt,
        public string $updatedAt,
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            name: (string) $row['name'],
            url: (string) $row['url'],
            city: (string) $row['city'],
            status: (string) $row['status'],
            logo: $row['logo'] !== null ? (string) $row['logo'] : null,
            createdAt: (string) $row['created_at'],
            updatedAt: (string) $row['updated_at'],
        );
    }
}