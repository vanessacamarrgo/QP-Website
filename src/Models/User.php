<?php

declare(strict_types=1);

namespace App\Models;

final class User
{
    public function __construct(
        public int    $id,
        public string $name,
        public string $email,
        public string $status,
        public string $createdAt,
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            id:        (int)($row['id'] ?? 0),
            name:      (string)($row['name'] ?? ''),
            email:     (string)($row['email'] ?? ''),
            status:    (string)($row['status'] ?? 'active'),
            createdAt: (string)($row['created_at'] ?? ''),
        );
    }
}