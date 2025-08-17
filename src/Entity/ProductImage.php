<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\ProductImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductImageRepository::class)]
#[ORM\HasLifecycleCallbacks]
class ProductImage
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private ?UuidInterface $id = null;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?Product $product = null;

    /**
     * Источник хранения: external|local
     */
    #[ORM\Column(length: 20, options: ['default' => 'external'])]
    private string $storage = 'external';

    /**
     * Внешний URL (если storage = external)
     */
    #[ORM\Column(length: 1024, nullable: true)]
    #[Assert\Url(protocols: ['http', 'https'])]
    private ?string $url = null;

    /**
     * Относительный путь для локального файла (если storage = local),
     * например: "products/ballade-dream/main.jpg"
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $localPath = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $alt = null;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $sortOrder = 0;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isPrimary = false;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    /**
     * Простая валидация: при external должен быть url; при local — localPath
     */
    #[Assert\Callback]
    public function validateSource(\Symfony\Component\Validator\Context\ExecutionContextInterface $context): void
    {
        if ($this->storage === 'external') {
            if (!$this->url) {
                $context->buildViolation('Для внешнего изображения поле url обязательно.')
                    ->atPath('url')
                    ->addViolation();
            }
        } elseif ($this->storage === 'local') {
            if (!$this->localPath) {
                $context->buildViolation('Для локального изображения поле localPath обязательно.')
                    ->atPath('localPath')
                    ->addViolation();
            }
        } else {
            $context->buildViolation('Недопустимый тип storage. Разрешено: external|local.')
                ->atPath('storage')
                ->addViolation();
        }
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function touchUpdatedAt(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
        if (!$this->createdAt) {
            $this->createdAt = new \DateTimeImmutable();
        }
    }

    /**
     * Универсальный getter для фронта: отдаёт абсолютный URL для внешнего или
     * публичный путь для локального (через baseUploads, по умолчанию "/uploads")
     */
    public function getPublicUrl(string $baseUploads = '/uploads'): ?string
    {
        return match ($this->storage) {
            'external' => $this->url,
            'local'    => $this->localPath ? rtrim($baseUploads, '/') . '/' . ltrim($this->localPath, '/') : null,
            default    => null,
        };
    }

    // --- Getters / Setters ---

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;
        return $this;
    }

    public function getStorage(): string
    {
        return $this->storage;
    }

    public function setStorage(string $storage): self
    {
        $this->storage = $storage;
        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;
        return $this;
    }

    public function getLocalPath(): ?string
    {
        return $this->localPath;
    }

    public function setLocalPath(?string $localPath): self
    {
        $this->localPath = $localPath;
        return $this;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(?string $alt): self
    {
        $this->alt = $alt;
        return $this;
    }

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(int $sortOrder): self
    {
        $this->sortOrder = $sortOrder;
        return $this;
    }

    public function isPrimary(): bool
    {
        return $this->isPrimary;
    }

    public function setIsPrimary(bool $isPrimary): self
    {
        $this->isPrimary = $isPrimary;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}