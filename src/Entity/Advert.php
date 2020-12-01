<?php

namespace App\Entity;

use App\Repository\AdvertRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;



/**
 * @ApiResource(
 *     collectionOperations={"get", "post"},
 *     itemOperations={"get"},
 *     normalizationContext={"groups"={"advert:read"}},
 *     denormalizationContext={"groups"={"advert:write"}}
 * )
 *
 *  @ApiFilter(OrderFilter::class, properties={"publishedAt", "price"}, arguments={"orderParameterName"="order"})
 *  @ApiFilter(RangeFilter::class, properties={"price"})
 * @ApiFilter(SearchFilter::class, properties={"category.id": "exact"})
 *
 * @ORM\Entity(repositoryClass=AdvertRepository::class)
 */
class Advert
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @Groups({"advert:read"})
     */
    private $id;

    /**
     * * @Assert\Length (
     *      min = 3,
     *      max = 100,
     *      minMessage = "Your title must be at least {{ limit }} characters long",
     *      maxMessage = "Your title cannot be longer than {{ limit }} characters",
     * )
     * @ORM\Column(type="string", length=100)
     *
     * @Groups({"advert:read", "advert:write"})
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     *
     * @Assert\Length(max="1200",maxMessage = "Your content cannot be longer than {{ limit }} characters",)
     *
     * @Groups({"advert:read", "advert:write"})
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"advert:read", "advert:write"})
     */
    private $author;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"advert:read", "advert:write"})
     *
     */
    private $email;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="adverts")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Groups({"advert:read", "advert:write"})
     */
    private $category;

    /**
     * @ORM\Column(type="float")
     *
     * @Assert\Range(
     *      min = 1,
     *      max = 1000000,
     *      notInRangeMessage = "The price must be between {{ min }}€ and {{ max }}€",
     * )
     *
     * @Groups({"advert:read", "advert:write"})
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"advert:read"})
     */
    private $state = 'submitted';

    /**
     * @ORM\Column(type="datetime")
     *
     * @Groups({"advert:read"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @Groups({"advert:read"})
     */
    private $publishedAt;

    /**
     * @ORM\OneToMany(targetEntity=Picture::class, mappedBy="advert")
     *
     * @Groups({"advert:read", "advert:write"})
     */
    private $pictures;

    public function __construct()
    {
        $this->pictures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeInterface $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * @return Collection|Picture[]
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function addPicture(Picture $picture): self
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures[] = $picture;
            $picture->setAdvert($this);
        }

        return $this;
    }

    public function removePicture(Picture $picture): self
    {
        if ($this->pictures->removeElement($picture)) {
            // set the owning side to null (unless already changed)
            if ($picture->getAdvert() === $this) {
                $picture->setAdvert(null);
            }
        }

        return $this;
    }

}
