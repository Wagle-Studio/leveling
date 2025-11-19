<?php

namespace App\Libs\Conversation\Factory;

use App\Libs\Conversation\Payload\PayloadInterface;
use App\Libs\Conversation\ConversationTypes;
use App\Libs\Conversation\Payload\BuildObjectiveStepsPayload;
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
            ConversationTypes::BuildObjectiveSteps->name => BuildObjectiveStepsPayload::class,
        ];
    }

    public function handle(ConversationTypes $promptType, object $result): PayloadInterface
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
