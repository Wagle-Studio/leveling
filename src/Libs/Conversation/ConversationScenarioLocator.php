<?php

namespace App\Libs\Queue;

use App\Domain\Core\Interface\ServiceLocatorInterface;
use App\Domain\ProgressionEngine\ProgressionScenarioLocator;
use App\Libs\Conversation\ConversationEnum;
use Psr\Container\ContainerInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

final class ConversationScenarioLocator implements ServiceSubscriberInterface
{
    public function __construct(
        protected ContainerInterface $locator,
        protected ObjectMapperInterface $objectMapper,
        protected ValidatorInterface $validator
    ) {}

    public static function getSubscribedServices(): array
    {
        return [
            ConversationEnum::SCENARIO_PORTAL_BUILD->value => ProgressionScenarioLocator::class,
        ];
    }

    public function handle(ConversationEnum $conversationEnum, object $payload): void
    {
        $convTypeName = $conversationEnum->value;

        if (!$this->locator->has($convTypeName)) {
            throw new \InvalidArgumentException(sprintf('["%s"] Unknown conversation: "%s".', self::class, $convTypeName));
        }

        /** @var ServiceLocatorInterface */
        $serviceLocator = $this->locator->get($convTypeName);
        $serviceLocator->handleConversation($conversationEnum, $payload);
    }
}
