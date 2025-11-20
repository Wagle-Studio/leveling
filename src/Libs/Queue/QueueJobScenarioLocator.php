<?php

namespace App\Libs\Queue;

use App\Domain\Core\Interface\ServiceLocatorInterface;
use App\Domain\ProgressionEngine\ProgressionScenarioLocator;
use App\Entity\QueueJob;
use App\Libs\Queue\QueueJobEnum;
use Psr\Container\ContainerInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

final class QueueJobScenarioLocator implements ServiceSubscriberInterface
{
    public function __construct(
        protected ContainerInterface $locator,
        protected ObjectMapperInterface $objectMapper,
        protected ValidatorInterface $validator
    ) {}

    public static function getSubscribedServices(): array
    {
        return [
            QueueJobEnum::SCENARIO_PORTAL_BUILD->value => ProgressionScenarioLocator::class,
        ];
    }

    public function handle(QueueJob $queueJob): void
    {
        $jobTypeName = $queueJob->getType();

        if (!$this->locator->has($jobTypeName)) {
            throw new \InvalidArgumentException(sprintf('["%s"] Unknown queue job: "%s".', self::class, $jobTypeName));
        }

        /** @var ServiceLocatorInterface */
        $serviceLocator = $this->locator->get($jobTypeName);
        $serviceLocator->handleQueueJob($queueJob);
    }
}
