<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserBadgeRepository")
 */
class UserBadge
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Badge", inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     */
    private $badge;

    /**
     * @ORM\Column(type="date")
     */
    private $givenDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBadge(): ?Badge
    {
        return $this->badge;
    }

    public function setBadge(?Badge $badge): self
    {
        $this->badge = $badge;

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
