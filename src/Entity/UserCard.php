<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserCardRepository")
 */
class UserCard
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Card", inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     */
    private $card;

    /**
     * @ORM\Column(type="date")
     */
    private $givenDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCard(): ?Card
    {
        return $this->card;
    }

    public function setCard(?Card $card): self
    {
        $this->card = $card;

        return $this;
    }

    public function getGivenDate(): ?\DateTimeInterface
    {
        return $this->givenDate;
    }

    public function setGivenDate(\DateTimeInterface $givenDate): self
    {
        $this->givenDate = $givenDate;

        return $this;
    }
}
