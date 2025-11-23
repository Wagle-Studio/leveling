<?php

namespace App\Libs\Domain\ProgressionEngine\Scenario\SkillDiscover;

use Symfony\Component\Validator\Constraints as Assert;

final class SkillDiscoverResult
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Valid]
        public readonly array $suggestions = [],
    ) {}
}

final class SkillDiscoverSuggestion
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        public readonly string $domain_label,

        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        public readonly string $branch_label,

        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        public readonly string $skill_label,
    ) {}
}
