<?php

namespace App\Entity;

use App\Repository\DomainRepository;
use App\Trait\SluggableTrait;
use App\Trait\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: DomainRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Objective
{
    use TimestampableTrait;
    use SluggableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['objective.read'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['objective.read'])]
    private ?int $difficulty = null;

    #[ORM\Column]
    #[Groups(['objective.read'])]
    private ?int $duration = null;

    #[ORM\ManyToOne(inversedBy: 'objectives')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Skill $skill = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setDifficulty(int $difficulty): static
    {
        $this->difficulty = $difficulty;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getSkill(): ?Skill
    {
        return $this->skill;
    }

    public function setSkill(?Skill $skill): static
    {
        $this->skill = $skill;

        return $this;
    }

    public function getDifficulty(): ?int
    {
        return $this->difficulty;
    }
}
