<?php

namespace App\Libs\Conversation;

use App\Domain\ProgressionEngine\Scenario\PortalBuild\ProgressionScenarioEnums;

enum ConversationEnum: string
{
    case SCENARIO_PORTAL_BUILD = ProgressionScenarioEnums::SCENARIO_PORTAL_BUILD->value;
}
