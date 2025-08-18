<?php
declare(strict_types=1);
namespace App\Entity;

use App\Repository\AddressRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: AddressRepository::class)]
class Address
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups(['admin:cart'])]
    private ?UuidInterface $id = null;

    #[ORM\ManyToOne(targetEntity: Customer::class, inversedBy: 'addresses')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private Customer $customer;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['admin:cart'])]
    private ?string $line1 = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['admin:cart'])]
    private ?string $line2 = null;

    #[ORM\Column(length: 100)]
    #[Groups(['admin:cart'])]
    private string $city;

    #[ORM\Column(length: 100)]
    #[Groups(['admin:cart'])]
    private string $region;

    #[ORM\Column(length: 20)]
    #[Groups(['admin:cart'])]
    private string $zip;

    public function __construct()
    {
        $this->city = 'Город';
        $this->region = 'Регион';
        $this->zip = 'Индекс';
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;
        return $this;
    }

    public function getLine1(): string
    {
        return $this->line1;
    }

    public function setLine1(string $line1): self
    {
        $this->line1 = $line1;
        return $this;
    }

    public function getLine2(): ?string
    {
        return $this->line2;
    }

    public function setLine2(?string $line2): self
    {
        $this->line2 = $line2;
        return $this;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;
        return $this;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function setRegion(string $region): self
    {
        $this->region = $region;
        return $this;
    }

    public function getZip(): string
    {
        return $this->zip;
    }

    public function setZip(string $zip): self
    {
        $this->zip = $zip;
        return $this;
    }
}