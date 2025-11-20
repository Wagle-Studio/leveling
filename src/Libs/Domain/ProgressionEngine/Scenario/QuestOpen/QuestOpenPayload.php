<?php

namespace App\Libs\Domain\ProgressionEngine\Scenario\QuestOpen;

use App\Libs\Queue\JobPayloadInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class QuestOpenPayload implements JobPayloadInterface
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Positive]
        public readonly int $step_id,
    ) {}

    public function toArray(): array
    {
        return [
            'step_id' => $this->step_id,
        ];
    }
}
