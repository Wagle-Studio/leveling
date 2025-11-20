<?php

namespace App\Libs\Domain\ProgressionEngine;

use App\Libs\Domain\Core\Interface\ServiceLocatorInterface;
use App\Libs\Domain\ProgressionEngine\Scenario\PortalOpen\PortalOpenScenario;
use App\Libs\Domain\ProgressionEngine\Scenario\QuestOpen\QuestOpenScenario;
use App\Entity\QueueJob;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

/**
 * @implements ServiceLocatorInterface<QueueJob>
 */
final class ProgressionScenarioLocator implements ServiceLocatorInterface, ServiceSubscriberInterface
{
    public function __construct(
        private readonly ContainerInterface $container
    ) {}

    public static function getSubscribedServices(): array
    {
        return [
            ProgressionScenarioEnums::SCENARIO_PORTAL_OPEN->value => PortalOpenScenario::class,
            ProgressionScenarioEnums::SCENARIO_QUEST_OPEN->value => QuestOpenScenario::class,
        ];
    }

    public function handle(object $input): void
    {
        if (!$input instanceof QueueJob) {
            throw new \InvalidArgumentException(
                sprintf('["%s"] Input must be an instance of QueueJob.', self::class)
            );
        }

        $scenarioClass = $input->getType();

        if (!$this->container->has($scenarioClass)) {
            throw new \InvalidArgumentException(
                sprintf('["%s"] Unknown progression scenario: "%s".', self::class, $scenarioClass)
            );
        }

        $scenario = $this->container->get($scenarioClass);

        if (!$scenario instanceof ScenarioInterface) {
            throw new \InvalidArgumentException(
                sprintf('["%s"] Service "%s" must implement "%s".', self::class, $scenarioClass, ScenarioInterface::class)
            );
        }

        $scenario->run((object) $input->getPayload());
    }
}
