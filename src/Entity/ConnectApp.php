<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ConnectAppRepository")
 */
class ConnectApp
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $apiKey;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Connection", mappedBy="app")
     */
    private $connections;

    public function __construct()
    {
        $this->connections = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    public function setApiKey(string $apiKey): self
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * @return Collection|Connection[]
     */
    public function getConnections(): Collection
    {
        return $this->connections;
    }

    public function addConnection(Connection $connection): self
    {
        if (!$this->connections->contains($connection)) {
            $this->connections[] = $connection;
            $connection->setApp($this);
        }

        return $this;
    }

    public function removeConnection(Connection $connection): self
    {
        if ($this->connections->contains($connection)) {
            $this->connections->removeElement($connection);
            // set the owning side to null (unless already changed)
            if ($connection->getApp() === $this) {
                $connection->setApp(null);
            }
        }

        return $this;
    }
}
