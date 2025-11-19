<?php

namespace App\Libs\Queue\Dispatcher;

use Psr\Container\ContainerInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

abstract class QueueAbstractDispatcher implements ServiceSubscriberInterface
{
    public function __construct(
        protected ContainerInterface $locator,
        protected ObjectMapperInterface $objectMapper,
        protected ValidatorInterface $validator
    ) {}
}
