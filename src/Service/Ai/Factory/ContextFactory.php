<?php

namespace App\Service\Ai\Factory;

use App\Service\Ai\Context\ContextInterface;
use App\Service\Ai\Context\StepsGenerateContext;
use App\Service\Ai\PromptTypes;
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
            'StepsGenerate' => StepsGenerateContext::class,
        ];
    }

    public function handle(PromptTypes $promptType, ...$params): ContextInterface
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
