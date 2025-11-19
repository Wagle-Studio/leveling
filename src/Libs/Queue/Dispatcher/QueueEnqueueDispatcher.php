<?php

namespace App\Libs\Queue\Dispatcher;

use App\Libs\Queue\Job\BuildObjectiveStepsJob;
use App\Libs\Queue\Job\QueueJobInterface;
use App\Libs\Queue\Payload\QueuePayloadInterface;
use App\Libs\Queue\QueueJobEnum;

final class QueueEnqueueDispatcher extends QueueAbstractDispatcher
{
    public static function getSubscribedServices(): array
    {
        return [
            QueueJobEnum::buildObjectiveSteps->value => BuildObjectiveStepsJob::class,
        ];
    }

    public function dispatch(QueueJobEnum $jobType, QueuePayloadInterface $payload): void
    {
        $jobTypeName = $jobType->value;

        if (!$this->locator->has($jobTypeName)) {
            throw new \InvalidArgumentException(sprintf('["%s"] Unknown queue job: "%s".', self::class, $jobTypeName));
        }

        $errors = $this->validator->validate($payload);

        if (count($errors) > 0) {
            $errorMsg = sprintf('["%s"] Invalid queue job: %s', self::class, (string) $errors);
            throw new \InvalidArgumentException($errorMsg);
        }

        /** @var QueueJobInterface */
        $jobHandler = $this->locator->get($jobTypeName);
        $jobHandler->enqueue($payload);
    }
}
