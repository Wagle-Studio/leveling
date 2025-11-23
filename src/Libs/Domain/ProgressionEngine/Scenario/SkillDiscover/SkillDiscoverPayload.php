<?php

namespace App\Libs\Domain\ProgressionEngine\Scenario\SkillDiscover;

use App\Libs\Queue\JobPayloadInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class SkillDiscoverPayload implements JobPayloadInterface
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        public readonly string $message,
    ) {}

    public function toArray(): array
    {
        return [
            'message' => $this->message,
        ];
    }
}
