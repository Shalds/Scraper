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

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ModelProduct", inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $model;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Site", inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Site;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateChange;

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

    public function getModel(): ?ModelProduct
    {
        return $this->model;
    }

    public function setModel(?ModelProduct $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getSite(): ?Site
    {
        return $this->Site;
    }

    public function setSite(?Site $Site): self
    {
        $this->Site = $Site;

        return $this;
    }

    public function getDateChange(): ?\DateTimeInterface
    {
        return $this->dateChange;
    }

    public function setDateChange(\DateTimeInterface $dateChange): self
    {
        $this->dateChange = $dateChange;

        return $this;
    }
}
