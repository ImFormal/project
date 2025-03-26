<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Put;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ApiResource(
    formats: ['json' => 'application/json'],
    operations: [
        new Get(
            uriTemplate: '/product/{id}', 
            requirements: ['id' => '\d+'],
            ),
        new GetCollection(
            uriTemplate: '/products',
            ),
        new Post(
            uriTemplate: '/product',
            status: 201
        ),
        new Delete(
            uriTemplate: '/product',
            status: 204
        ),
        new Put(
            uriTemplate: '/product/{id}', 
            requirements: ['id' => '\d+'],
            status: 201
        ),
    ],
    order: ['id' => 'ASC', 'name' => 'ASC'],
    paginationEnabled: true
)]

class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column]
    private ?float $price = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }
}
