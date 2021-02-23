<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PromoRepository")
 */
class Promo
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
    private $type;

    /**
     * @ORM\Column(type="string", length=7)
     */
    private $textColor;

    /**
     * @ORM\Column(type="string", length=7)
     */
    private $color;

    /**
     * @ORM\Column(type="integer")
     */
    private $buttonType;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $buttonData;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVisible;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $imagePath;

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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getTextColor(): ?string
    {
        return $this->textColor;
    }

    public function setTextColor(string $textColor): self
    {
        $this->textColor = $textColor;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getButtonType(): ?int
    {
        return $this->buttonType;
    }

    public function setButtonType(int $buttonType): self
    {
        $this->buttonType = $buttonType;

        return $this;
    }

    public function getButtonData(): ?string
    {
        return $this->buttonData;
    }

    public function setButtonData(string $buttonData): self
    {
        $this->buttonData = $buttonData;

        return $this;
    }

    public function getIsVisible(): ?bool
    {
        return $this->isVisible;
    }

    public function setIsVisible(bool $isVisible): self
    {
        $this->isVisible = $isVisible;

        return $this;
    }

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function setImagePath(string $imagePath): self
    {
        $this->imagePath = $imagePath;

        return $this;
    }

    public function getJSON() {
        return array(
            'id' => $this->id,
            'title' => $this->title,
            'type' => $this->type,
            'textColor' => $this->textColor,
            'color' => $this->color,
            'button' => array(
                'type' => $this->buttonType,
                'data' => $this->buttonData
            ),
            'isVisible' => $this->isVisible,
            'image_path' => $_ENV['ASSET_BASE_URL']."/".$_ENV['ASSET_PROMO_FOLDER']."/".$this->imagePath,
        );
    }
}
