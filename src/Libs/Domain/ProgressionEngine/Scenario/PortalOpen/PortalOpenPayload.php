<?php

namespace App\Libs\Domain\ProgressionEngine\Scenario\PortalOpen;

use App\Libs\Queue\JobPayloadInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class PortalOpenPayload implements JobPayloadInterface
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Positive]
        public readonly int $objective_id,
    ) {}

    public function toArray(): array
    {
        return [
            'objective_id' => $this->objective_id,
        ];
    }
}
