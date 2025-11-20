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
use Symfony\Contracts\Service\ServiceSubscriberInterface;

final class ProgressionScenarioLocatorSave implements ServiceLocatorInterface, ServiceSubscriberInterface
{
    public function __construct(
        protected ContainerInterface $locator,
        protected ValidatorInterface $validator,
        protected ObjectMapperInterface $objectMapper,
    ) {}

    public static function getSubscribedServices(): array
    {
        return [
            ProgressionScenarioEnums::SCENARIO_PORTAL_BUILD->value => PortalOpenScenario::class,
        ];
    }

    public function handleQueueJob(QueueJob $queueJob): void
    {
        $jobTypeName = $queueJob->getType();

        if (!$this->locator->has($jobTypeName)) {
            throw new \InvalidArgumentException(sprintf('["%s"] Unknown scenario: "%s".', self::class, $jobTypeName));
        }

        /** @var ScenarioInterface */
        $scenario = $this->locator->get($jobTypeName);

        /** @var QueuePayloadInterface */
        $payload = $this->objectMapper->map((object) $queueJob->getPayload(), ProgressionScenarioEnums::from($jobTypeName)->getQueuePayloadClass());

        $errors = $this->validator->validate($payload);

        if (count($errors) > 0) {
            $errorMsg = sprintf('["%s"] Invalid queue process: %s', self::class, (string) $errors);
            throw new \InvalidArgumentException($errorMsg);
        }

        $scenario->run($payload);
    }

    public function handleConversation(ConversationEnum $conversationEnum, object $payload): void
    {
        if (!$this->locator->has($conversationEnum->value)) {
            throw new \InvalidArgumentException(sprintf('["%s"] Unknown conversation scenario: "%s".', self::class, $conversationEnum->value));
        }

        /** @var ScenarioInterface */
        $scenario = $this->locator->get($conversationEnum->value);
        $scenario->run($payload);
    }
}
