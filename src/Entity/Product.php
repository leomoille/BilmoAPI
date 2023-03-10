<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "detailProduct",
 *          parameters = { "id" = "expr(object.getId())" }
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups="getProducts")
 * )
 *
 * @Hateoas\Relation(
 *      "delete",
 *      href = @Hateoas\Route(
 *          "deleteProduct",
 *          parameters = { "id" = "expr(object.getId())" },
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups="getProducts")
 * )
 *
 * @Hateoas\Relation(
 *      "update",
 *      href = @Hateoas\Route(
 *          "updateProduct",
 *          parameters = { "id" = "expr(object.getId())" },
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups="getProducts")
 * )
 *
 */
#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getProducts", "getUsers"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getProducts", "getUsers"])]
    #[Assert\NotBlank(message: "Le produit doit avoir un nom")]
    #[Assert\Type('string', message: "La valeur {{ value }} n'est pas un {{ type }} valide")]
    #[Assert\Length(
        min: 1,
        max: 255,
        minMessage: "Le nom du produit doit faire au moins {{ limit }} caractères",
        maxMessage: "Le nom du produit ne peut pas faire plus de {{ limit }} caractères"
    )]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(["getProducts", "getUsers"])]
    #[Assert\NotBlank(message: "Le produit doit avoir un prix")]
    #[Assert\Positive(message: "Le prix ne peut pas être négatif ou égale à 0")]
    #[Assert\Type('int', message: "La valeur {{ value }} n'est pas un {{ type }} valide")]
    private ?int $price = null;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'products', cascade: ['persist'])]
    #[Groups(["getProducts"])]
    private Collection $users;

    #[ORM\ManyToOne(inversedBy: 'products')]
    private ?Client $client = null;

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

    public function getPrice(): ?float
    {
        return $this->price / 100;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price * 100;

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
}
