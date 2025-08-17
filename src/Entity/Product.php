<?php
declare(strict_types=1);
namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes\Property;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups(['product:list'])]
    #[Property(type: 'string', example: '15e7d25b-87db-4dad-b3ba-fc71f7d4effa')]
    private ?UuidInterface $id = null;

    #[ORM\ManyToOne(targetEntity: Category::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Category $category;

    #[ORM\Column(length: 255)]
    #[Groups(['product:list'])]
    #[Property(example: 'Тюльпан лилиецветный Балладе Дрим')]
    private string $titleRu;

    #[ORM\Column(length: 255, nullable: true)]
    #[Property(example: 'Ballade Tulip')]
    private ?string $latinName = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $heightCm = null;

    #[ORM\Column(length: 255, unique: true)]
    private string $slug;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $metaTitle = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $metaDescription = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, ProductImage>
     */
    #[ORM\OneToMany(
        targetEntity: ProductImage::class,
        mappedBy: 'product',
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $images;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->images = new ArrayCollection();
    }

    /**
     * @internal
     */
    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $now = new \DateTimeImmutable();
        $this->updatedAt = $now;
    }


    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getTitleRu(): string
    {
        return $this->titleRu;
    }

    public function setTitleRu(string $titleRu): self
    {
        $this->titleRu = $titleRu;

        return $this;
    }

    public function getLatinName(): ?string
    {
        return $this->latinName;
    }

    public function setLatinName(?string $latinName): self
    {
        $this->latinName = $latinName;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getHeightCm(): ?int
    {
        return $this->heightCm;
    }

    public function setHeightCm(?int $heightCm): self
    {
        $this->heightCm = $heightCm;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;


        return $this;
    }

    public function getMetaTitle(): ?string
    {
        return $this->metaTitle;
    }

    public function setMetaTitle(?string $metaTitle): self
    {
        $this->metaTitle = $metaTitle;

        return $this;
    }

    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    public function setMetaDescription(?string $metaDescription): self
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, ProductImage>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(ProductImage $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setProduct($this);
        }
        return $this;
    }

    public function removeImage(ProductImage $image): self
    {
        if ($this->images->removeElement($image)) {
            // снимаем связь, чтобы orphanRemoval сработал
            if ($image->getProduct() === $this) {
                $image->setProduct(null);
            }
        }
        return $this;
    }
}
