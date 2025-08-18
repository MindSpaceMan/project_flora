<?php
declare(strict_types=1);
namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups(['admin:cart'])]
    private ?UuidInterface $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['admin:cart'])]
    private string $name;

    #[ORM\Column(length: 255, unique: true, nullable: true)]
    #[Groups(['admin:cart'])]
    private ?string $email = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['admin:cart'])]
    private ?string $phone = null;

    /** @var Collection<int, Address> */
    #[ORM\OneToMany(targetEntity: Address::class, mappedBy: 'customer', cascade: ['persist','remove'])]
    #[Groups(['admin:cart'])]
    private Collection $addresses;

    /** @var Collection<int, Order> */
    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'customer', cascade: ['persist','remove'])]
    private Collection $orders;

    #[ORM\Column(type: 'text', nullable: false)]
    #[Groups(['admin:cart'])]
    private ?string $comment = null;

    public function __construct()
    {
        $this->addresses = new ArrayCollection();
        $this->orders    = new ArrayCollection();
        $this->comment = 'Новый заказ';
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getAddresses(): Collection
    {
        return $this->addresses;
    }

    public function addAddress(Address $address): self
    {
        if (!$this->addresses->contains($address)) {
            $this->addresses->add($address);
            $address->setCustomer($this);
        }
        return $this;
    }

    public function removeAddress(Address $address): self
    {
        if ($this->addresses->removeElement($address)) {
            if ($address->getCustomer() === $this) {
                $address->setCustomer(null);
            }
        }
        return $this;
    }

    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setCustomer($this);
        }
        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            if ($order->getCustomer() === $this) {
                $order->setCustomer(null);
            }
        }
        return $this;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;
        return $this;
    }

}