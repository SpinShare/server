<?php
    // src/AppBundle/Entity/User.php

    namespace App\Entity;

    use Doctrine\Common\Collections\ArrayCollection;
    use Doctrine\Common\Collections\Collection;
    use FOS\UserBundle\Model\User as BaseUser;
    use Doctrine\ORM\Mapping as ORM;

    /**
     * @ORM\Entity
     * @ORM\Table(name="fos_user")
     */
    class User extends BaseUser
    {
        /**
         * @ORM\Id
         * @ORM\Column(type="integer")
         * @ORM\GeneratedValue(strategy="AUTO")
         */
        protected $id;

        /**
         * @ORM\Column(type="string", length=255, nullable=true)
         */
        private $coverReference;

        /**
         * @ORM\Column(type="boolean", nullable=true)
         */
        private $isVerified;

        /**
         * @ORM\Column(type="boolean", nullable=true)
         */
        private $isPatreon;

        /**
         * @ORM\OneToMany(targetEntity="App\Entity\SongReview", mappedBy="user", orphanRemoval=true)
         */
        private $reviews;

        /**
         * @ORM\ManyToMany(targetEntity="App\Entity\SongSpinPlay", mappedBy="user")
         */
        private $spinPlays;

        /**
         * @ORM\Column(type="string", length=6, nullable=true)
         */
        private $connectCode;

        /**
         * @ORM\OneToMany(targetEntity="App\Entity\Connection", mappedBy="user")
         */
        private $connections;

        /**
         * @ORM\OneToMany(targetEntity="App\Entity\UserNotification", mappedBy="user")
         */
        private $userNotifications;

        public function __construct()
        {
            parent::__construct();
            $this->reviews = new ArrayCollection();
            $this->spinPlays = new ArrayCollection();
            // your own logic

            $this->connections = new ArrayCollection();
            $this->userNotifications = new ArrayCollection();        }

        public function getCoverReference(): ?string
        {
            return $this->coverReference;
        }
    
        public function setCoverReference(?string $coverReference): self
        {
            $this->coverReference = $coverReference;
    
            return $this;
        }

        public function getIsVerified(): ?bool
        {
            return $this->isVerified;
        }

        public function setIsVerified(bool $isVerified): self
        {
            $this->isVerified = $isVerified;

            return $this;
        }

        public function getIsPatreon(): ?bool
        {
            return $this->isPatreon;
        }

        public function setIsPatreon(bool $isPatreon): self
        {
            $this->isPatreon = $isPatreon;

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
                $spinPlay->addUser($this);
            }

            return $this;
        }

        public function removeSpinPlay(SongSpinPlay $spinPlay): self
        {
            if ($this->spinPlays->contains($spinPlay)) {
                $this->spinPlays->removeElement($spinPlay);
                $spinPlay->removeUser($this);
            }

            return $this;
        }

        public function getJSON() {
            $response = array(
                'id' => $this->id,
                'username' => $this->username,
                'coverReference' => $this->coverReference,
                'isVerified' => $this->isVerified,
                'isPatreon' => $this->isPatreon
            );
    
            return $response;
        }

        public function getConnectCode(): ?string
        {
            return $this->connectCode;
        }

        public function setConnectCode(?string $connectCode): self
        {
            $this->connectCode = $connectCode;

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
                $connection->setUser($this);
            }

            return $this;
        }

        public function removeConnection(Connection $connection): self
        {
            if ($this->connections->contains($connection)) {
                $this->connections->removeElement($connection);
                // set the owning side to null (unless already changed)
                if ($connection->getUser() === $this) {
                    $connection->setUser(null);
                }
            }

            return $this;
        }

        /**
         * @return Collection|UserNotification[]
         */
        public function getUserNotifications(): Collection
        {
            return $this->userNotifications;
        }

        public function addUserNotification(UserNotification $userNotification): self
        {
            if (!$this->userNotifications->contains($userNotification)) {
                $this->userNotifications[] = $userNotification;
                $userNotification->setUser($this);
            }

            return $this;
        }

        public function removeUserNotification(UserNotification $userNotification): self
        {
            if ($this->userNotifications->contains($userNotification)) {
                $this->userNotifications->removeElement($userNotification);
                // set the owning side to null (unless already changed)
                if ($userNotification->getUser() === $this) {
                    $userNotification->setUser(null);
                }
            }

            return $this;
        }
    }