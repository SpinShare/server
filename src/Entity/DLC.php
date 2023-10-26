<?php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DLCRepository")
 */
class DLC
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
    private $identifier;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $storeLink;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\DLCHash", mappedBy="dlc", orphanRemoval=true)
     */
    private $hashes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Song", mappedBy="dlc")
     */
    private $songs;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __construct()
    {
        $this->hashes = new ArrayCollection();
        $this->songs = new ArrayCollection();
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

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getStoreLink(): ?string
    {
        return $this->storeLink;
    }

    public function setStoreLink(string $storeLink): self
    {
        $this->storeLink = $storeLink;

        return $this;
    }

    /**
     * @return Collection|DLCHash[]
     */
    public function getHashes(): Collection
    {
        return $this->hashes;
    }

    public function addHash(DLCHash $hash): self
    {
        if (!$this->hashes->contains($hash)) {
            $this->hashes[] = $hash;
            $hash->setDLC($this);
        }

        return $this;
    }

    public function removeHash(DLCHash $hash): self
    {
        if ($this->hashes->contains($hash)) {
            $this->hashes->removeElement($hash);
            // set the owning side to null (unless already changed)
            if ($hash->getDLC() === $this) {
                $hash->setDLC(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Song[]
     */
    public function getSongs(): Collection
    {
        return $this->songs;
    }

    public function addSong(Song $song): self
    {
        if (!$this->songs->contains($song)) {
            $this->songs[] = $song;
            $song->setDLC($this);
        }

        return $this;
    }

    public function removeSong(Song $song): self
    {
        if ($this->songs->contains($song)) {
            $this->songs->removeElement($song);
            // set the owning side to null (unless already changed)
            if ($song->getDLC() === $this) {
                $song->setDLC(null);
            }
        }

        return $this;
    }

    public function getJSON() {
        return array(
            'id' => $this->id,
            'identifier' => $this->identifier,
            'title' => $this->title,
            'storeLink' => $this->storeLink,
        );
    }
}
