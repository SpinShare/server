<?php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SongSpinPlayRepository")
 */
class SongSpinPlay
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="spinPlays")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Song", inversedBy="spinPlays")
     * @ORM\JoinColumn(nullable=false)
     */
    private $song;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $videoUrl;

    /**
     * @ORM\Column(type="datetime")
     */
    private $submitDate;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    public function __construct()
    {
    }

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

    public function getSong(): ?Song
    {
        return $this->song;
    }

    public function setSong(?Song $song): self
    {
        $this->song = $song;

        return $this;
    }

    public function getVideoUrl(): ?string
    {
        return $this->videoUrl;
    }

    public function setVideoUrl(string $videoUrl): self
    {
        $this->videoUrl = $videoUrl;

        return $this;
    }

    public function getVideoThumbnail(): ?string
    {
        $videoUrlSplit = explode('/' , $this->videoUrl);
        $splitCount = count($videoUrlSplit);

        return $this->fetch_highest_res(str_replace("watch?v=", "", $videoUrlSplit[$splitCount - 1]));
    }

    public function getSubmitDate(): ?DateTimeInterface
    {
        return $this->submitDate;
    }

    public function setSubmitDate(DateTimeInterface $submitDate): self
    {
        $this->submitDate = $submitDate;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    // https://stackoverflow.com/questions/18029550/fetch-youtube-highest-thumbnail-resolution
    function fetch_highest_res($videoid) {
        $resolutions = array('maxresdefault', 'hqdefault', 'mqdefault');     
        foreach($resolutions as $res) {
            $imgUrl = "https://i.ytimg.com/vi/$videoid/$res.jpg";
            if(@getimagesize(($imgUrl))) 
                return $imgUrl;
        }
    }

    public function getJSON() {
        return array(
            'id' => $this->id,
            'user' => $this->user->getJSON(),
            'videoUrl' => $this->videoUrl,
            'videoThumbnail' => $this->getVideoThumbnail(),
            'submitDate' => $this->submitDate,
            'isActive' => $this->isActive
        );
    }
}
