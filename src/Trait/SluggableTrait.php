<?php

namespace App\Trait;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\String\Slugger\AsciiSlugger;

trait SluggableTrait
{
    #[ORM\Column(length: 255)]
    #[Groups(['common.read'])]
    private string $label;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['common.read'])]
    private string $slug;

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function generateSlug(): void
    {
        if (isset($this->label)) {
            $slugger = new AsciiSlugger();
            $this->slug = $slugger->slug($this->label)->lower()->toString();
        }
    }
}
