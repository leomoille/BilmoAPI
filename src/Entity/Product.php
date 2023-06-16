<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ApiResource(
    shortName: 'Products',
    description: 'Produit du catalogue BilMo',
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Put(),
        new Patch(),
        new Delete(),
    ],
    normalizationContext: [
        'groups' => ['product:read'],
    ],
    denormalizationContext: [
        'groups' => ['product:write'],
    ],
    security: "is_granted('ROLE_USER')"
)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Nom du produit.
     */
    #[ORM\Column(length: 255)]
    #[Groups(['product:read', 'product:write'])]
    #[Assert\NotBlank(message: 'Le produit doit avoir un nom')]
    #[Assert\Type('string', message: "La valeur {{ value }} n'est pas un {{ type }} valide")]
    #[Assert\Length(
        min: 1,
        max: 255,
        minMessage: 'Le nom du produit doit faire au moins {{ limit }} caractères',
        maxMessage: 'Le nom du produit ne peut pas faire plus de {{ limit }} caractères'
    )]
    private ?string $name = null;

    /**
     * Prix du produit, en centime.
     */
    #[ORM\Column]
    #[Groups(['product:read', 'product:write'])]
    #[Assert\NotBlank(message: 'Le produit doit avoir un prix')]
    #[Assert\Positive(message: 'Le prix ne peut pas être négatif ou égale à 0')]
    #[Assert\Type('int', message: "La valeur {{ value }} n'est pas un {{ type }} valide")]
    private ?int $price = null;

    /**
     * Utilisateur possédant ce produit.
     *
     * @var Collection|ArrayCollection
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'products', cascade: ['persist'])]
    #[Groups(['product:read', 'product:write'])]
    private Collection $users;

    /**
     * Client propriétaire du produit.
     */
    #[ORM\ManyToOne(inversedBy: 'products')]
    private ?Client $client = null;

    /**
     * Marque du produit.
     */
    #[ORM\Column(length: 255)]
    #[Groups(['product:read', 'product:write'])]
    #[Assert\Type('string', message: "La valeur {{ value }} n'est pas un {{ type }} valide")]
    #[Assert\Length(
        min: 1,
        max: 255,
        minMessage: 'La marque du produit doit faire au moins {{ limit }} caractères',
        maxMessage: 'La marque du produit ne peut pas faire plus de {{ limit }} caractères'
    )]
    #[Assert\NotBlank(message: 'Le produit doit avoir une marque')]
    private ?string $brand = null;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addProduct($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            $user->removeProduct($this);
        }

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

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }
}
