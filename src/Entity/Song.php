<?php

namespace App\Entity;

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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fileReference;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $srtbOriginalName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $coverOriginalName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $audioOriginalName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tags;

    /**
     * @ORM\Column(type="integer")
     */
    private $views;

    /**
     * @ORM\Column(type="integer")
     */
    private $downloads;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isExplicit;

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

    public function getFileReference(): ?string
    {
        return $this->fileReference;
    }

    public function setFileReference(?string $fileReference): self
    {
        $this->fileReference = $fileReference;

        return $this;
    }

    public function getSRTBOriginalName(): ?string
    {
        return $this->srtbOriginalName;
    }

    public function setSRTBOriginalName(?string $srtbOriginalName): self
    {
        $this->srtbOriginalName = $srtbOriginalName;

        return $this;
    }

    public function getCoverOriginalName(): ?string
    {
        return $this->coverOriginalName;
    }

    public function setCoverOriginalName(?string $coverOriginalName): self
    {
        $this->coverOriginalName = $coverOriginalName;

        return $this;
    }

    public function getAudioOriginalName(): ?string
    {
        return $this->audioOriginalName;
    }

    public function setAudioOriginalName(?string $audioOriginalName): self
    {
        $this->audioOriginalName = $audioOriginalName;

        return $this;
    }

    public function getTags(): ?string
    {
        return $this->tags;
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
}
