<?php

namespace App\Service\Ai\Factory;

use App\Service\Ai\Payload\PayloadInterface;
use App\Service\Ai\Payload\StepsGeneratePayload;
use App\Service\Ai\PromptTypes;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

final class PayloadFactory implements PayloadFactoryInterface, ServiceSubscriberInterface
{
    public function __construct(
        private ContainerInterface $locator,
    ) {}

    public static function getSubscribedServices(): array
    {
        return [
            'StepsGenerate' => StepsGeneratePayload::class,
        ];
    }

    public function handle(PromptTypes $promptType, object $result): PayloadInterface
    {
        if (!$this->locator->has($promptType->name)) {
            throw new \InvalidArgumentException(
                sprintf('["%s"] Unknown payload: "%s".', self::class, $promptType->name)
            );
        }

        /** @var PayloadInterface */
        $handler = $this->locator->get($promptType->name);
        $handler->initialize($result);

        return $handler;
    }
}
