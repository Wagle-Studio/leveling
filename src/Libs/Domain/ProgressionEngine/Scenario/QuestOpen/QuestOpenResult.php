<?php

namespace App\Libs\Domain\ProgressionEngine\Scenario\QuestOpen;

use Symfony\Component\Validator\Constraints as Assert;

final class QuestOpenResult
{
    public function __construct(
        public readonly ?string $id,

        public readonly ?string $step_id,

        #[Assert\NotBlank]
        #[Assert\Length(max: 80)]
        public readonly string $before_label,

        #[Assert\NotBlank]
        #[Assert\Length(min: 40, max: 500)]
        public readonly string $before_scene,

        #[Assert\NotBlank]
        #[Assert\Length(max: 80)]
        public readonly string $success_label,

        #[Assert\NotBlank]
        #[Assert\Length(min: 40, max: 500)]
        public readonly string $success_scene,

        #[Assert\NotBlank]
        #[Assert\Length(max: 80)]
        public readonly string $failure_label,

        #[Assert\NotBlank]
        #[Assert\Length(min: 40, max: 500)]
        public readonly string $failure_scene,
    ) {}
}

