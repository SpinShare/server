<?php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DLCHashRepository")
 */
class DLCHash
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
    private $hash;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\DLC", inversedBy="hashes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $dlc;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }

    public function getDLC(): ?DLC
    {
        return $this->dlc;
    }

    public function setDLC(?DLC $dlc): self
    {
        $this->dlc = $dlc;

        return $this;
    }
}
