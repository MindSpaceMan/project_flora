<?php
declare(strict_types=1);
namespace App\Entity;

use App\Repository\AddressRepository;
use App\Repository\OrderRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Customer;
use App\Entity\OrderItem;
use OpenApi\Attributes\Property;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: 'duple_order')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Property(type: 'string', example: '15e7d25b-87db-4dad-b3ba-fc71f7d4effa')]
    #[Groups(['cart:read', 'admin:cart'])]
    private ?UuidInterface $id = null;

    #[ORM\ManyToOne(targetEntity: Customer::class, inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['admin:cart'])]
    private Customer $customer;

    #[ORM\Column(length: 20, options: ['default' => 'cart'])]
    #[Groups(['cart:read', 'admin:cart'])]
    private string $status = 'cart';

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['admin:cart'])]
    private \DateTimeImmutable $createdAt;

    /** @var Collection<int, OrderItem> */
    #[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'order', cascade: ['persist','remove'], orphanRemoval: true)]
    #[Groups(['cart:read', 'admin:cart'])]
    private Collection $items;

    #[ORM\Column(length: 128, unique: true, nullable: true)]
    #[Groups(['cart:read', 'admin:cart'])]
    private ?string $cartTokenHash = null;


    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->items     = new ArrayCollection();
        // $status_example = cart|sent|cancelled|paid
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

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(OrderItem $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
            $item->setOrder($this);
        }
        return $this;
    }

    public function removeItem(OrderItem $item): self
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getOrder() === $this) {
                $item->setOrder(null);
            }
        }
        return $this;
    }
    public function setCartTokenHash(?string $hash): self { $this->cartTokenHash = $hash; return $this; }
    public function getCartTokenHash(): ?string { return $this->cartTokenHash; }
}