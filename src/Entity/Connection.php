<?php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ConnectionRepository")
 */
class Connection
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="connections")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $connectDate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ConnectApp", inversedBy="connections")
     */
    private $app;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $connectToken;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getConnectDate(): ?DateTimeInterface
    {
        return $this->connectDate;
    }

    public function setConnectDate(DateTimeInterface $connectDate): self
    {
        $this->connectDate = $connectDate;

        return $this;
    }

    public function getApp(): ?ConnectApp
    {
        return $this->app;
    }

    public function setApp(?ConnectApp $app): self
    {
        $this->app = $app;

        return $this;
    }

    public function getConnectToken(): ?string
    {
        return $this->connectToken;
    }

    public function setConnectToken(string $connectToken): self
    {
        $this->connectToken = $connectToken;

        return $this;
    }
}
