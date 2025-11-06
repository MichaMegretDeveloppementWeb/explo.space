<?php

namespace App\DTO\Admin\Auth;

readonly class LoginCredentialsDTO
{
    public function __construct(
        public string $email,
        public string $password,
        public bool $remember,
        public string $ip,
    ) {}

    /**
     * Créer une instance depuis un array
     *
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            email: $data['email'],
            password: $data['password'],
            remember: $data['remember'] ?? false,
            ip: $data['ip'],
        );
    }
}
