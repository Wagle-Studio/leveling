<?php

namespace App\Domain\ProgressionEngine;

use App\Domain\Core\Interface\ScenarioInterface;
use App\Domain\Core\Interface\ServiceLocatorInterface;
use App\Domain\ProgressionEngine\Scenario\PortalBuild\PortalOpenScenario;
use App\Domain\ProgressionEngine\Scenario\PortalBuild\ProgressionScenarioEnums;
use App\Entity\QueueJob;
use App\Libs\Conversation\ConversationEnum;
use App\Libs\Queue\QueuePayloadInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ProgressionScenarioLocator implements ServiceLocatorInterface
{
    public function __construct(
        protected ContainerInterface $locator,
        protected ValidatorInterface $validator,
        protected ObjectMapperInterface $objectMapper,
    ) {}

    public static function getSubscribedServices(): array
    {
        return [
            PortalOpenScenario::class => PortalOpenScenario::class,
        ];
    }

    public function handleQueueJob(QueueJob $job): void
    {
        $scenarioEnum = ProgressionScenarioEnums::from($job->getType());
        $scenarioClassName = $scenarioEnum->getScenarioClass();

        if (!$this->locator->has($scenarioClassName)) {
            throw new \InvalidArgumentException(sprintf('["%s"] Unknown progression scenario: "%s".', self::class, $scenarioClassName));
        }

        /** @var ScenarioInterface */
        $scenario = $this->locator->get($scenarioClassName);

        /** @var QueuePayloadInterface */
        $payload = $this->objectMapper->map((object) $job->getPayload(), $scenarioEnum->getQueuePayloadDtoClass());

        $errors = $this->validator->validate($payload);

        if (count($errors) > 0) {
            $errorMsg = sprintf('["%s"] Invalid queue process: %s', self::class, (string) $errors);
            throw new \InvalidArgumentException($errorMsg);
        }

        $scenario->run($payload);
    }

    public function handleConversation(ConversationEnum $conversationEnum, object $payload): void
    {
        $scenarioEnum = ProgressionScenarioEnums::from($conversationEnum->value);
        $scenarioConvClassName = $scenarioEnum->getConvScenarioClass();

        if (!$this->locator->has($scenarioConvClassName)) {
            throw new \InvalidArgumentException(sprintf('["%s"] Unknown progression scenario: "%s".', self::class, $scenarioConvClassName));
        }

        /** @var ScenarioInterface */
        $scenario = $this->locator->get($scenarioConvClassName);
        $scenario->run($payload);
    }
}
