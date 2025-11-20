<?php

namespace App\Domain\ProgressionEngine\Scenario\PortalBuild;

enum ProgressionScenarioEnums: string
{
    case SCENARIO_PORTAL_BUILD = 'SCENARIO_PORTAL_BUILD';

    public function getQueuePayloadDtoClass(): string
    {
        return match ($this) {
            self::SCENARIO_PORTAL_BUILD => PortalOpenQueuePayloadDto::class,
        };
    }

    public function getConvScenarioClass(): string
    {
        return match ($this) {
            self::SCENARIO_PORTAL_BUILD => PortalOpenConvScenario::class,
        };
    }

    public function getScenarioClass(): string
    {
        return match ($this) {
            self::SCENARIO_PORTAL_BUILD => PortalOpenScenario::class,
        };
    }
}
