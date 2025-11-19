<?php

namespace App\Libs\Queue\Dispatcher;

use App\Entity\QueueJob;
use App\Libs\Queue\Payload\QueuePayloadInterface;
use App\Libs\Queue\Process\BuildObjectiveStepsProcess;
use App\Libs\Queue\Process\QueueProcessInterface;
use App\Libs\Queue\QueueJobEnum;

final class QueueExecutionDispatcher extends QueueAbstractDispatcher
{
    public static function getSubscribedServices(): array
    {
        return [
            QueueJobEnum::buildObjectiveSteps->value => BuildObjectiveStepsProcess::class,
        ];
    }

    public function dispatch(QueueJob $job): void
    {
        $jobTypeName = $job->getType();

        if (!$this->locator->has($jobTypeName)) {
            throw new \InvalidArgumentException(sprintf('["%s"] Unknown queue process: "%s".', self::class, $jobTypeName));
        }

        /** @var QueuePayloadInterface */
        $payload = $this->objectMapper->map((object) $job->getPayload(), QueueJobEnum::from($jobTypeName)->getPayloadClass());

        $errors = $this->validator->validate($payload);

        if (count($errors) > 0) {
            $errorMsg = sprintf('["%s"] Invalid queue process: %s', self::class, (string) $errors);
            throw new \InvalidArgumentException($errorMsg);
        }

        /** @var QueueProcessInterface */
        $processHandler = $this->locator->get($jobTypeName);
        $processHandler->process($payload);
    }
}
