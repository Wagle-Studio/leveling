<?php

namespace App\Libs\Queue;

use App\Libs\Domain\ProgressionEngine\ProgressionScenarioEnum;

enum QueueJobEnum: string
{
    case SCENARIO_PORTAL_OPEN = ProgressionScenarioEnum::SCENARIO_PORTAL_OPEN->value;
    case SCENARIO_QUEST_OPEN = ProgressionScenarioEnum::SCENARIO_QUEST_OPEN->value;
}
