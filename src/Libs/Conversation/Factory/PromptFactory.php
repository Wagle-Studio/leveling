<?php

namespace App\Libs\Conversation\Factory;

use App\Libs\Conversation\Context\ContextInterface;
use App\Libs\Conversation\Prompt\PromptInterface;
use App\Libs\Conversation\ConversationTypes;
use App\Libs\Conversation\Prompt\BuildObjectiveStepsPrompt;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

final class PromptFactory implements PromptFactoryInterface, ServiceSubscriberInterface
{
    public function __construct(
        private ContainerInterface $locator,
    ) {}

    public static function getSubscribedServices(): array
    {
        return [
            ConversationTypes::BuildObjectiveSteps->name => BuildObjectiveStepsPrompt::class,
        ];
    }

    public function handle(ConversationTypes $promptType, ContextInterface $context): PromptInterface
    {
        if (!$this->locator->has($promptType->name)) {
            throw new \InvalidArgumentException(
                sprintf('["%s"] Unknown prompt: "%s".', self::class, $promptType->name)
            );
        }

        /** @var PromptInterface */
        $handler = $this->locator->get($promptType->name);
        $handler->initialize($context);

        return $handler;
    }
}
