<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
final class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const string ROLE_USER = 'ROLE_USER';
    public const string ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * @param Uuid $id
     * @param non-empty-string $username
     * @param non-empty-string $password
     * @param array<string> $roles
     * @param non-empty-string $firstName
     * @param non-empty-string $lastName
     * @param non-empty-string $email
     * @param non-empty-string $phone
     */
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: UuidType::NAME)]
        private Uuid $id,
        #[ORM\Column(unique: true)]
        private string $username,
        #[ORM\Column]
        #[\SensitiveParameter]
        private string $password,
        #[ORM\Column]
        private string $firstName,
        #[ORM\Column]
        private string $lastName,
        #[ORM\Column]
        private string $middleName,
        #[ORM\Column]
        private string $email,
        #[ORM\Column]
        private string $phone,
        #[ORM\Column(type: Types::JSON)]
        private array $roles,
    ) {}

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function eraseCredentials(): void {}

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getMiddleName(): string
    {
        return $this->middleName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }
}
