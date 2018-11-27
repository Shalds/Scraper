<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 */
class Product
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $priceMag;

    /**
     * @ORM\Column(type="float")
     */
    private $priceFinal;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sizeDispo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;

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

    public function getPriceMag(): ?float
    {
        return $this->priceMag;
    }

    public function setPriceMag(float $priceMag): self
    {
        $this->priceMag = $priceMag;

        return $this;
    }

    public function getPriceFinal(): ?float
    {
        return $this->priceFinal;
    }

    public function setPriceFinal(float $priceFinal): self
    {
        $this->priceFinal = $priceFinal;

        return $this;
    }

    public function getSizeDispo(): ?string
    {
        return $this->sizeDispo;
    }

    public function setSizeDispo(string $sizeDispo): self
    {
        $this->sizeDispo = $sizeDispo;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }
}
