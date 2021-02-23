<?php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SongRepository")
 */
class Song
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $subtitle;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $artist;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $charter;

    /**
     * @ORM\Column(type="integer")
     */
    private $uploader;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fileReference;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tags;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"default": 0})
     */
    private $views;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"default": 0})
     */
    private $downloads;

    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default": false})
     */
    private $isExplicit;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"default": 0})
     */
    private $publicationStatus;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hasEasyDifficulty;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hasNormalDifficulty;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hasHardDifficulty;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hasExtremeDifficulty;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hasXDDifficulty;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"default": 0})
     */
    private $easyDifficulty;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"default": 0})
     */
    private $normalDifficulty;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"default": 0})
     */
    private $hardDifficulty;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"default": 0})
     */
    private $expertDifficulty;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"default": 0})
     */
    private $XDDifficulty;

    /**
     * @ORM\Column(type="datetime")
     */
    private $uploadDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $updateHash;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SongReview", mappedBy="song", orphanRemoval=true)
     */
    private $reviews;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SongSpinPlay", mappedBy="song", orphanRemoval=true)
     */
    private $spinPlays;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\SongPlaylist", mappedBy="songs")
     */
    private $songPlaylists;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updateDate;

    public function __construct()
    {
        $this->reviews = new ArrayCollection();
        $this->spinPlays = new ArrayCollection();
        $this->songPlaylists = new ArrayCollection();
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

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function setSubtitle(?string $subtitle): self
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    public function getArtist(): ?string
    {
        return $this->artist;
    }

    public function setArtist(string $artist): self
    {
        $this->artist = $artist;

        return $this;
    }

    public function getCharter(): ?string
    {
        return $this->charter;
    }

    public function setCharter(string $charter): self
    {
        $this->charter = $charter;

        return $this;
    }

    public function getUploader(): ?int
    {
        return $this->uploader;
    }

    public function setUploader(int $uploader): self
    {
        $this->uploader = $uploader;

        return $this;
    }

    public function getFileReference(): ?string
    {
        return $this->fileReference;
    }

    public function setFileReference(?string $fileReference): self
    {
        $this->fileReference = $fileReference;

        return $this;
    }

    public function getTags(): ?string
    {
        return $this->tags;
    }

    public function getTagsArray()
    {
        return array_map('trim', explode(",", $this->tags));
    }

    public function setTags(?string $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    public function getViews(): ?int
    {
        return $this->views;
    }

    public function setViews(int $views): self
    {
        $this->views = $views;

        return $this;
    }

    public function getDownloads(): ?int
    {
        return $this->downloads;
    }

    public function setDownloads(int $downloads): self
    {
        $this->downloads = $downloads;

        return $this;
    }

    public function getIsExplicit(): ?bool
    {
        return $this->isExplicit;
    }

    public function setIsExplicit(bool $isExplicit): self
    {
        $this->isExplicit = $isExplicit;

        return $this;
    }

    public function getPublicationStatus(): ?int
    {
        return $this->publicationStatus;
    }

    public function setPublicationStatus(int $publicationStatus): self
    {
        $this->publicationStatus = $publicationStatus;

        return $this;
    }

    public function getHasEasyDifficulty(): ?bool
    {
        return $this->hasEasyDifficulty;
    }

    public function setHasEasyDifficulty(bool $hasEasyDifficulty): self
    {
        $this->hasEasyDifficulty = $hasEasyDifficulty;

        return $this;
    }

    public function getHasNormalDifficulty(): ?bool
    {
        return $this->hasNormalDifficulty;
    }

    public function setHasNormalDifficulty(bool $hasNormalDifficulty): self
    {
        $this->hasNormalDifficulty = $hasNormalDifficulty;

        return $this;
    }

    public function getHasHardDifficulty(): ?bool
    {
        return $this->hasHardDifficulty;
    }

    public function setHasHardDifficulty(bool $hasHardDifficulty): self
    {
        $this->hasHardDifficulty = $hasHardDifficulty;

        return $this;
    }

    public function getHasExtremeDifficulty(): ?bool
    {
        return $this->hasExtremeDifficulty;
    }

    public function setHasExtremeDifficulty(bool $hasExtremeDifficulty): self
    {
        $this->hasExtremeDifficulty = $hasExtremeDifficulty;

        return $this;
    }

    public function getHasXDDifficulty(): ?bool
    {
        return $this->hasXDDifficulty;
    }

    public function setHasXDDifficulty(bool $hasXDDifficulty): self
    {
        $this->hasXDDifficulty = $hasXDDifficulty;

        return $this;
    }

    public function getEasyDifficulty(): ?int
    {
        return $this->easyDifficulty;
    }

    public function setEasyDifficulty(?int $easyDifficulty): self
    {
        if($easyDifficulty != null) {
            $this->easyDifficulty = max(0, min(99, $easyDifficulty));
        } else {
            $this->easyDifficulty = $easyDifficulty;
        }

        return $this;
    }

    public function getNormalDifficulty(): ?int
    {
        return $this->normalDifficulty;
    }

    public function setNormalDifficulty(?int $normalDifficulty): self
    {
        if($normalDifficulty != null) {
            $this->normalDifficulty = max(0, min(99, $normalDifficulty));
        } else {
            $this->normalDifficulty = $normalDifficulty;
        }

        return $this;
    }

    public function getHardDifficulty(): ?int
    {
        return $this->hardDifficulty;
    }

    public function setHardDifficulty(?int $hardDifficulty): self
    {
        if($hardDifficulty != null) {
            $this->hardDifficulty = max(0, min(99, $hardDifficulty));
        } else {
            $this->hardDifficulty = $hardDifficulty;
        }

        return $this;
    }

    public function getExpertDifficulty(): ?int
    {
        return $this->expertDifficulty;
    }

    public function setExpertDifficulty(?int $expertDifficulty): self
    {
        if($expertDifficulty != null) {
            $this->expertDifficulty = max(0, min(99, $expertDifficulty));
        } else {
            $this->expertDifficulty = $expertDifficulty;
        }

        return $this;
    }

    public function getXDDifficulty(): ?int
    {
        return $this->XDDifficulty;
    }

    public function setXDDifficulty(?int $XDDifficulty): self
    {
        if($XDDifficulty != null) {
            $this->XDDifficulty = max(0, min(99, $XDDifficulty));
        } else {
            $this->XDDifficulty = $XDDifficulty;
        }

        return $this;
    }

    public function getUploadDate(): ?DateTimeInterface
    {
        return $this->uploadDate;
    }

    public function setUploadDate(DateTimeInterface $uploadDate): self
    {
        $this->uploadDate = $uploadDate;

        return $this;
    }

    public function getUpdateHash(): ?string
    {
        return $this->updateHash;
    }

    public function setUpdateHash(?string $updateHash): self
    {
        $this->updateHash = $updateHash;

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

    /**
     * @return Collection|SongReview[]
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(SongReview $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews[] = $review;
            $review->setSong($this);
        }

        return $this;
    }

    public function removeReview(SongReview $review): self
    {
        if ($this->reviews->contains($review)) {
            $this->reviews->removeElement($review);
            // set the owning side to null (unless already changed)
            if ($review->getSong() === $this) {
                $review->setSong(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|SongSpinPlay[]
     */
    public function getSpinPlays(): Collection
    {
        return $this->spinPlays;
    }

    public function addSpinPlay(SongSpinPlay $spinPlay): self
    {
        if (!$this->spinPlays->contains($spinPlay)) {
            $this->spinPlays[] = $spinPlay;
            $spinPlay->setSong($this);
        }

        return $this;
    }

    public function removeSpinPlay(SongSpinPlay $spinPlay): self
    {
        if ($this->spinPlays->contains($spinPlay)) {
            $this->spinPlays->removeElement($spinPlay);
            // set the owning side to null (unless already changed)
            if ($spinPlay->getSong() === $this) {
                $spinPlay->setSong(null);
            }
        }

        return $this;
    }

    public function getJSON() {
        return array(
            'id' => $this->id,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'artist' => $this->artist,
            'charter' => $this->charter,
            'uploader' => $this->uploader,
            'fileReference' => $this->fileReference,
            'tags' => $this->getTagsArray(),
            'views' => $this->views,
            'downloads' => $this->downloads,
            'isExplicit' => $this->isExplicit,
            'publicationStatus' => $this->publicationStatus,
            'hasEasyDifficulty' => $this->hasEasyDifficulty,
            'hasNormalDifficulty' => $this->hasNormalDifficulty,
            'hasHardDifficulty' => $this->hasHardDifficulty,
            'hasExtremeDifficulty' => $this->hasExtremeDifficulty,
            'hasXDDifficulty' => $this->hasXDDifficulty,
            'easyDifficulty' => $this->easyDifficulty,
            'normalDifficulty' => $this->normalDifficulty,
            'hardDifficulty' => $this->hardDifficulty,
            'expertDifficulty' => $this->expertDifficulty,
            'XDDifficulty' => $this->XDDifficulty,
            'uploadDate' => $this->uploadDate,
            'updateDate' => $this->updateDate,
            'updateHash' => $this->updateHash,
            'description' => $this->description,
            'cover' => $_ENV['ASSET_BASE_URL']."/".$_ENV['ASSET_COVER_FOLDER']."/".$this->getFileReference().".png"
        );
    }

    /**
     * @return Collection|SongPlaylist[]
     */
    public function getSongPlaylists(): Collection
    {
        return $this->songPlaylists;
    }

    public function addSongPlaylist(SongPlaylist $songPlaylist): self
    {
        if (!$this->songPlaylists->contains($songPlaylist)) {
            $this->songPlaylists[] = $songPlaylist;
            $songPlaylist->addSong($this);
        }

        return $this;
    }

    public function removeSongPlaylist(SongPlaylist $songPlaylist): self
    {
        if ($this->songPlaylists->contains($songPlaylist)) {
            $this->songPlaylists->removeElement($songPlaylist);
            $songPlaylist->removeSong($this);
        }

        return $this;
    }

    public function getUpdateDate(): ?\DateTimeInterface
    {
        return $this->updateDate;
    }

    public function setUpdateDate(?\DateTimeInterface $updateDate): self
    {
        $this->updateDate = $updateDate;

        return $this;
    }
}
