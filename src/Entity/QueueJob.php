<?php

namespace App\Entity;

use App\Libs\Queue\QueueJobStatusEnum;
use App\Repository\QueueJobRepository;
use App\Trait\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: QueueJobRepository::class)]
#[ORM\Index(name: 'idx_status_id', columns: ['status', 'id'])]
#[ORM\HasLifecycleCallbacks]
class QueueJob
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['queue_job.read'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Groups(['queue_job.read'])]
    private ?string $type = null;

    #[ORM\Column(type: 'json')]
    private array $payload = [];

    #[ORM\Column(length: 20)]
    #[Groups(['queue_job.read'])]
    private string $status = QueueJobStatusEnum::PENDING->value;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Groups(['queue_job.read'])]
    private ?\DateTimeImmutable $processedAt = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['queue_job.read'])]
    private ?string $errorMessage = null;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    #[Groups(['queue_job.read'])]
    private int $attempts = 0;

    #[ORM\Column(type: 'integer', options: ['default' => 3])]
    private int $maxRetries = 3;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function setPayload(array $payload): static
    {
        $this->payload = $payload;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(QueueJobStatusEnum $status): static
    {
        $this->status = $status->value;

        return $this;
    }

    public function getProcessedAt(): ?\DateTimeImmutable
    {
        return $this->processedAt;
    }

    public function setProcessedAt(?\DateTimeImmutable $processedAt): static
    {
        $this->processedAt = $processedAt;

        return $this;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function setErrorMessage(?string $errorMessage): static
    {
        $this->errorMessage = $errorMessage;

        return $this;
    }

    public function getAttempts(): int
    {
        return $this->attempts;
    }

    public function incrementAttempts(): static
    {
        $this->attempts++;

        return $this;
    }

    public function getMaxRetries(): int
    {
        return $this->maxRetries;
    }

    public function setMaxRetries(int $maxRetries): static
    {
        $this->maxRetries = $maxRetries;

        return $this;
    }

    public function canRetry(): bool
    {
        return $this->attempts < $this->maxRetries;
    }
}
