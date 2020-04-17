<?php
    // src/AppBundle/Entity/User.php

    namespace App\Entity;

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

        public function __construct()
        {
            parent::__construct();
            // your own logic
        }

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
    }