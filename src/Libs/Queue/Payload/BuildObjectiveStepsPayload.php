<?php

namespace App\Libs\Queue\Payload;

use Symfony\Component\Validator\Constraints as Assert;

final class BuildObjectiveStepsPayload implements QueuePayloadInterface
{
    #[Assert\Positive]
    public int $objective_id;

    public function getObjectiveId(): int
    {
        return $this->objective_id;
    }

    public function toArray(): array
    {
        return [
            'objective_id' => $this->objective_id,
        ];
    }
}
