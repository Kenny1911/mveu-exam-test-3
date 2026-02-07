<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'orders')]
final class Order
{
    private function __construct(
        #[ORM\Id]
        #[ORM\Column(type: UuidType::NAME)]
        public Uuid $id,
        #[ORM\ManyToOne(targetEntity: User::class)]
        #[ORM\JoinColumn(nullable: false)]
        public User $customer,
        #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
        public \DateTimeImmutable $orderTimestamp,
        #[ORM\Column(type: Types::TEXT)]
        public string $address,
        #[ORM\Column]
        public string $contactPhone,
        #[ORM\Column(enumType: OrderStatus::class)]
        public OrderStatus $status,
        #[ORM\Column(type: Types::TEXT)]
        public string $comment,
        #[ORM\Column(enumType: Service::class)]
        public Service $service,
        #[ORM\Column(enumType: PayType::class)]
        public PayType $payType,
        #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
        public \DateTimeImmutable $createdAt,
        #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
        public \DateTimeImmutable $updatedAt,
    ) {}

    public static function new(
        Uuid $id,
        User $customer,
        \DateTimeImmutable $orderTimestamp,
        string $address,
        string $contactPhone,
        Service $service,
        PayType $payType,
        \DateTimeImmutable $timestamp,
    ): self {
        return new self(
            id: $id,
            customer: $customer,
            orderTimestamp: $orderTimestamp,
            address: $address,
            contactPhone: $contactPhone,
            status: OrderStatus::NEW,
            comment: '',
            service: $service,
            payType: $payType,
            createdAt: $timestamp,
            updatedAt: $timestamp,
        );
    }

    public function changeStatus(OrderStatus $status, string $comment, \DateTimeImmutable $timestamp): void
    {
        $this->status = $status;
        $this->comment = $comment;
        $this->updatedAt = $timestamp;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getCustomer(): User
    {
        return $this->customer;
    }

    public function getOrderTimestamp(): \DateTimeImmutable
    {
        return $this->orderTimestamp;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getContactPhone(): string
    {
        return $this->contactPhone;
    }

    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function getService(): Service
    {
        return $this->service;
    }

    public function getPayType(): PayType
    {
        return $this->payType;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
