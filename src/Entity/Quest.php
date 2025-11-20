<?php

namespace App\Entity;

use App\Repository\QuestRepository;
use App\Trait\TimestampableTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuestRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Quest
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Step::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Step $step = null;

    #[ORM\Column(length: 255)]
    private ?string $beforeLabel = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $beforeScene = null;

    #[ORM\Column(length: 255)]
    private ?string $successLabel = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $successScene = null;

    #[ORM\Column(length: 255)]
    private ?string $failureLabel = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $failureScene = null;

    public static function create(
        Step $step,
        string $beforeLabel,
        string $beforeScene,
        string $successLabel,
        string $successScene,
        string $failureLabel,
        string $failureScene,

    ): self {
        $quest = new self();
        $quest->step = $step;
        $quest->beforeLabel = $beforeLabel;
        $quest->beforeScene = $beforeScene;
        $quest->successLabel = $successLabel;
        $quest->successScene = $successScene;
        $quest->failureLabel = $failureLabel;
        $quest->failureScene = $failureScene;

        return $quest;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStep(): ?Step
    {
        return $this->step;
    }

    public function setStep(?Step $step): static
    {
        $this->step = $step;

        return $this;
    }

    public function getBeforeLabel(): ?string
    {
        return $this->beforeLabel;
    }

    public function setBeforeLabel(string $beforeLabel): static
    {
        $this->beforeLabel = $beforeLabel;

        return $this;
    }

    public function getBeforeScene(): ?string
    {
        return $this->beforeScene;
    }

    public function setBeforeScene(string $beforeScene): static
    {
        $this->beforeScene = $beforeScene;

        return $this;
    }

    public function getSuccessLabel(): ?string
    {
        return $this->successLabel;
    }

    public function setSuccessLabel(string $successLabel): static
    {
        $this->successLabel = $successLabel;

        return $this;
    }

    public function getSuccessScene(): ?string
    {
        return $this->successScene;
    }

    public function setSuccessScene(string $successScene): static
    {
        $this->successScene = $successScene;

        return $this;
    }

    public function getFailureLabel(): ?string
    {
        return $this->failureLabel;
    }

    public function setFailureLabel(string $failureLabel): static
    {
        $this->failureLabel = $failureLabel;

        return $this;
    }

    public function getFailureScene(): ?string
    {
        return $this->failureScene;
    }

    public function setFailureScene(string $failureScene): static
    {
        $this->failureScene = $failureScene;

        return $this;
    }
}
