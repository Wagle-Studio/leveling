<?php

namespace App\Libs\Domain\ProgressionEngine\Scenario\SkillDiscover;

use App\Libs\Ai\AiProviderInterface;
use App\Libs\Domain\ProgressionEngine\ScenarioInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SkillDiscoverScenario implements ScenarioInterface
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly ObjectMapperInterface $objectMapper,
        private readonly SkillDiscoverPromptBuilder $skillDiscoverPromptBuilder,
        private readonly AiProviderInterface $aiAgent,
    ) {}

    public function run(object $payload): mixed
    {
        /** @var SkillDiscoverPayload $payload */
        $payload = $this->objectMapper->map($payload, SkillDiscoverPayload::class);

        $payloadErrors = $this->validator->validate($payload);

        if (count($payloadErrors) > 0) {
            throw new \InvalidArgumentException(
                sprintf('["%s"] Invalid progression scenario payload: %s', self::class, (string) $payloadErrors)
            );
        }

        $this->skillDiscoverPromptBuilder->initialize($payload->message);

        $systemInstructions = $this->skillDiscoverPromptBuilder->getSystemInstructions();
        $userInstructions = $this->skillDiscoverPromptBuilder->getUserInstructions();

        $convResult = $this->aiAgent->send($systemInstructions, $userInstructions);

        /** @var SkillDiscoverResult $resultPayload */
        $resultPayload = $this->objectMapper->map($convResult, SkillDiscoverResult::class);

        $resultPayloadErrors = $this->validator->validate($resultPayload);

        if (count($resultPayloadErrors) > 0) {
            throw new \InvalidArgumentException(
                sprintf('["%s"] Invalid conversation result: %s', self::class, (string) $resultPayloadErrors)
            );
        }

        $suggestions = [];

        foreach ($resultPayload->suggestions as $payloadSuggestion) {
            /** @var SkillDiscoverSuggestion $suggestion */
            $suggestion = $this->objectMapper->map($payloadSuggestion, SkillDiscoverSuggestion::class);

            $suggestionErrors = $this->validator->validate($suggestion);

            if (count($suggestionErrors) > 0) {
                throw new \InvalidArgumentException(
                    sprintf('["%s"] Invalid conversation result: %s', self::class, (string) $suggestionErrors)
                );
            }

            $suggestions[] = $suggestion;
        }

        return $suggestions;
    }
}
