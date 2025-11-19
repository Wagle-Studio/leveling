<?php

namespace App\Libs\Queue;

use App\Libs\Queue\Payload\BuildObjectiveStepsPayload;

enum QueueJobEnum: string
{
    case buildObjectiveSteps = 'buildObjectiveSteps';


    public function getPayloadClass(): string
    {
        return match ($this) {
            self::buildObjectiveSteps => BuildObjectiveStepsPayload::class,
        };
    }
}
