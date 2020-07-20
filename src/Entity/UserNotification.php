<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserNotificationRepository")
 */
class UserNotification
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="userNotifications")
     */
    private $user;

    /**
     * @ORM\Column(type="integer")
     */
    private $notificationType;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $notificationData;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Song")
     */
    private $connectedSong;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $connectedUser;

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

    public function getNotificationType(): ?int
    {
        return $this->notificationType;
    }

    public function setNotificationType(int $notificationType): self
    {
        $this->notificationType = $notificationType;

        return $this;
    }

    public function getNotificationData(): ?string
    {
        return $this->notificationData;
    }

    public function setNotificationData(string $notificationData): self
    {
        $this->notificationData = $notificationData;

        return $this;
    }

    public function getConnectedSong(): ?Song
    {
        return $this->connectedSong;
    }

    public function setConnectedSong(?Song $connectedSong): self
    {
        $this->connectedSong = $connectedSong;

        return $this;
    }

    public function getConnectedUser(): ?User
    {
        return $this->connectedUser;
    }

    public function setConnectedUser(?User $connectedUser): self
    {
        $this->connectedUser = $connectedUser;

        return $this;
    }
}
