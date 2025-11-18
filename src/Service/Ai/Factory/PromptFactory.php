<?php

namespace App\Service\Ai\Factory;

use App\Service\Ai\Context\ContextInterface;
use App\Service\Ai\Prompt\PromptInterface;
use App\Service\Ai\Prompt\StepsGeneratePrompt;
use App\Service\Ai\PromptTypes;
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
            'StepsGenerate' => StepsGeneratePrompt::class,
        ];
    }

    public function handle(PromptTypes $promptType, ContextInterface $context): PromptInterface
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
