<?php

namespace App\Dto\User;

final class RegisterUserDto
{
    public function __construct(
        public readonly ?string $firstName,
        public readonly ?string $lastName,
        public readonly ?string $email,
        public readonly ?string $password,
        public readonly ?string $role,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $rawRole = $data['role'] ?? ($data['roles'][0] ?? null);

        return new self(
            self::normalizeString($data['first_name'] ?? null),
            self::normalizeString($data['last_name'] ?? null),
            self::normalizeString($data['email'] ?? null),
            self::normalizeString($data['password'] ?? null),
            self::normalizeRole($rawRole),
        );
    }

    /**
     * @return array<int, string>
     */
    public function validate(): array
    {
        $errors = [];

        if (!$this->firstName) {
            $errors[] = 'Le prenom est requis.';
        }

        if (!$this->lastName) {
            $errors[] = 'Le nom est requis.';
        }

        if (!$this->email) {
            $errors[] = 'L\'email est requis.';
        } elseif (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Le format de l\'email est invalide.';
        }

        if (!$this->password) {
            $errors[] = 'Le mot de passe est requis.';
        } elseif (strlen($this->password) < 8) {
            $errors[] = 'Le mot de passe doit contenir au moins 8 caracteres.';
        }

        if (!$this->role) {
            $errors[] = 'Le role est requis.';
        }

        return $errors;
    }

    private static function normalizeString(mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value !== '' ? $value : null;
    }

    private static function normalizeRole(mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }

        return match (strtolower(trim($value))) {
            'student', 'role_student' => 'ROLE_STUDENT',
            'teacher', 'role_teacher' => 'ROLE_TEACHER',
            'parent', 'role_parent' => 'ROLE_PARENT',
            'admin', 'role_admin' => 'ROLE_ADMIN',
            default => null,
        };
    }
}