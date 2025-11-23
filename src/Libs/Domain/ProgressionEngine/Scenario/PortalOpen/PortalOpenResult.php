<?php

namespace App\Libs\Domain\ProgressionEngine\Scenario\PortalOpen;

use Symfony\Component\Validator\Constraints as Assert;

final class PortalOpenResult
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Valid]
        public readonly array $steps = [],
    ) {}
}
