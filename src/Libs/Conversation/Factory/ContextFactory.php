<?php

namespace App\Libs\Conversation\Factory;

use App\Libs\Conversation\Context\BuildObjectiveStepsContext;
use App\Libs\Conversation\Context\ContextInterface;
use App\Libs\Conversation\ConversationTypes;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

final class ContextFactory implements ContextFactoryInterface, ServiceSubscriberInterface
{
    public function __construct(
        private ContainerInterface $locator,
    ) {}

    public static function getSubscribedServices(): array
    {
        return [
            ConversationTypes::BuildObjectiveSteps->name => BuildObjectiveStepsContext::class,
        ];
    }

    public function handle(ConversationTypes $promptType, ...$params): ContextInterface
    {
        if (!$this->locator->has($promptType->name)) {
            throw new \InvalidArgumentException(
                sprintf('["%s"] Unknown context: "%s".', self::class, $promptType->name)
            );
        }

        /** @var ContextInterface */
        $handler = $this->locator->get($promptType->name);
        $handler->initialize(...$params);

        return $handler;
    }
}
