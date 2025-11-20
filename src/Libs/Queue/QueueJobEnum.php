<?php

namespace App\Libs\Queue;

use App\Libs\Domain\ProgressionEngine\ProgressionScenarioEnums;

enum QueueJobEnum: string
{
    case SCENARIO_PORTAL_OPEN = ProgressionScenarioEnums::SCENARIO_PORTAL_OPEN->value;
    case SCENARIO_QUEST_OPEN = ProgressionScenarioEnums::SCENARIO_QUEST_OPEN->value;
}
