<?php

namespace App\Service\Ai\Context;

use App\Entity\Objective;

final class StepsGenerateContext implements ContextInterface
{
    private Objective $objective;

    public function initialize(...$params): void
    {
        if (!$params[0] instanceof Objective) {
            throw new \InvalidArgumentException(
                sprintf('["%s"] needs an instance of "%s". Received: "%s".', self::class, Objective::class, get_class($params[0]))
            );
        }

        $this->objective = $params[0];
    }

    public function getObjective(): Objective
    {
        return $this->objective;
    }
}
