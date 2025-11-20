<?php

namespace App\Libs\Domain\ProgressionEngine\Scenario\QuestOpen;

use App\Libs\Domain\ProgressionEngine\ScenarioInterface;
use App\Entity\Quest;
use App\Libs\Ai\AiProviderInterface;
use App\Repository\QuestRepository;
use App\Repository\StepRepository;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class QuestOpenScenario implements ScenarioInterface
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly ObjectMapperInterface $objectMapper,
        private readonly StepRepository $stepRepository,
        private readonly QuestRepository $questRepository,
        private readonly QuestOpenPromptBuilder $questOpenPromptBuilder,
        private readonly AiProviderInterface $aiAgent,
    ) {}

    public function run(object $payload): void
    {
        /** @var QuestOpenPayload $payload */
        $payload = $this->objectMapper->map($payload, QuestOpenPayload::class);

        $payloadErrors = $this->validator->validate($payload);

        if (count($payloadErrors) > 0) {
            throw new \InvalidArgumentException(
                sprintf('["%s"] Invalid progression scenario payload: %s', self::class, (string) $payloadErrors)
            );
        }

        $step = $this->stepRepository->find($payload->step_id);

        if ($step === null) {
            throw new \RuntimeException(
                sprintf('["%s"] Step with ID "%s" not found.', self::class, $payload->step_id)
            );
        }

        $this->questOpenPromptBuilder->initialize($step);

        $systemInstructions = $this->questOpenPromptBuilder->getSystemInstructions();
        $userInstructions = $this->questOpenPromptBuilder->getUserInstructions();

        $convResult = $this->aiAgent->send($systemInstructions, $userInstructions);

        /** @var QuestOpenResult $resultPayload */
        $resultPayload = $this->objectMapper->map($convResult, QuestOpenResult::class);

        $resultPayloadErrors = $this->validator->validate($resultPayload);

        if (count($resultPayloadErrors) > 0) {
            throw new \InvalidArgumentException(
                sprintf('["%s"] Invalid conversation result: %s', self::class, (string) $resultPayloadErrors)
            );
        }

        $quest = Quest::create(
            $step,
            $resultPayload->before_label,
            $resultPayload->before_scene,
            $resultPayload->success_label,
            $resultPayload->success_scene,
            $resultPayload->failure_label,
            $resultPayload->failure_scene,
        );

        $questErrors = $this->validator->validate($quest);

        if (count($questErrors) > 0) {
            throw new \InvalidArgumentException(
                sprintf('["%s"] Invalid conversation result: %s', self::class, (string) $questErrors)
            );
        }

        $this->questRepository->save($quest);
    }
}
