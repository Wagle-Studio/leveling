<?php

namespace App\Entity;

use App\Repository\StepRepository;
use App\Trait\SluggableTrait;
use App\Trait\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;

#[ORM\Entity(repositoryClass: StepRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Step
{
    use TimestampableTrait;
    use SluggableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['step.read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'steps')]
    #[ORM\JoinColumn(nullable: false)]
    #[Ignore]
    private ?Objective $objective = null;

    #[ORM\Column(length: 255)]
    #[Groups(['step.read'])]
    private ?string $instruction = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getObjective(): ?Objective
    {
        return $this->objective;
    }

    public function setObjective(?Objective $objective): static
    {
        $this->objective = $objective;

        return $this;
    }

    public function getInstruction(): ?string
    {
        return $this->instruction;
    }

    public function setInstruction(string $instruction): static
    {
        $this->instruction = $instruction;

        return $this;
    }
}
