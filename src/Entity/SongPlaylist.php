<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SongPlaylistRepository")
 */
class SongPlaylist
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
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fileReference;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isOfficial;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Song", inversedBy="songPlaylists")
     */
    private $songs;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="songPlaylists")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function __construct()
    {
        $this->songs = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getFileReference(): ?string
    {
        return $this->fileReference;
    }

    public function setFileReference(string $fileReference): self
    {
        $this->fileReference = $fileReference;

        return $this;
    }

    public function getIsOfficial(): ?bool
    {
        return $this->isOfficial;
    }

    public function setIsOfficial(bool $isOfficial): self
    {
        $this->isOfficial = $isOfficial;

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
        }

        return $this;
    }

    public function removeSong(Song $song): self
    {
        if ($this->songs->contains($song)) {
            $this->songs->removeElement($song);
        }

        return $this;
    }

    public function getJSON() {
        $songs = array();

        foreach($this->songs as $song) {
            $songs[] = $song->getJSON();
        }

        return array(
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'fileReference' => $this->fileReference,
            'user' => $this->user->getJSON(),
            'songs' => $songs,
            'isOfficial' => $this->isOfficial,
            'cover' => $_ENV['ASSET_BASE_URL']."/".$_ENV['ASSET_COVER_FOLDER']."/".$this->fileReference.".png"
        );
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
}
