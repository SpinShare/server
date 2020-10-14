<?php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ClientReleaseRepository")
 */
class ClientRelease
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $uploadDate;

    /**
     * @ORM\Column(type="integer")
     */
    private $majorVersion;

    /**
     * @ORM\Column(type="integer")
     */
    private $minorVersion;

    /**
     * @ORM\Column(type="integer")
     */
    private $patchVersion;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fileReference;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $platform;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getMajorVersion(): ?int
    {
        return $this->majorVersion;
    }

    public function setMajorVersion(int $majorVersion): self
    {
        $this->majorVersion = $majorVersion;

        return $this;
    }

    public function getMinorVersion(): ?int
    {
        return $this->minorVersion;
    }

    public function setMinorVersion(int $minorVersion): self
    {
        $this->minorVersion = $minorVersion;

        return $this;
    }

    public function getPatchVersion(): ?int
    {
        return $this->patchVersion;
    }

    public function setPatchVersion(int $patchVersion): self
    {
        $this->patchVersion = $patchVersion;

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

    public function getPlatform(): ?string
    {
        return $this->platform;
    }

    public function setPlatform(string $platform): self
    {
        $this->platform = $platform;

        return $this;
    }
}
