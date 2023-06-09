<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource(
    shortName: 'User',
    description: 'Utilisateurs',
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Put(),
        new Patch(),
        new Delete(),
    ],
    normalizationContext: [
        'groups' => ['user:read'],
    ],
    denormalizationContext: [
        'groups' => ['user:write'],
    ]
)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read', 'user:write'])]
    #[Assert\NotBlank(message: "L'utilisateur doit avoir un nom")]
    #[Assert\Length(
        min: 1,
        max: 255,
        minMessage: "Le nom de l'utilisateur doit faire au moins {{ limit }} caractères",
        maxMessage: "Le nom de l'utilisateur ne peut pas faire plus de {{ limit }} caractères"
    )]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read', 'user:write'])]
    #[Assert\NotBlank(message: "L'utilisateur doit avoir un email")]
    #[Assert\Email(message: "L'email {{ value }} n'est pas un email valide")]
    #[Assert\Length(
        min: 1,
        max: 255,
        minMessage: "L'email de l'utilisateur doit faire au moins {{ limit }} caractères",
        maxMessage: "L'email de l'utilisateur ne peut pas faire plus de {{ limit }} caractères"
    )]
    private ?string $email = null;

    #[ORM\ManyToMany(targetEntity: Product::class, inversedBy: 'users', cascade: ['persist'])]
    #[Groups(['user:read', 'user:write'])]
    private Collection $products;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[Groups(['user:read', 'user:write'])]
    private ?Client $client = null;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

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

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        $this->products->removeElement($product);

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }
}
