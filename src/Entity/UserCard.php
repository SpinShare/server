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
     * @ORM\ManyToOne(targetEntity="App\Entity\Card", inversedBy="userCards")
     */
    private $card;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="userCards")
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

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
