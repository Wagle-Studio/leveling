<?php

namespace App\Controller\Api;

use App\Entity\{Objective, Step};
use App\Repository\StepRepository;
use App\Service\Ai\{Agent\OpenAiAgent, Factory\ContextFactory, Factory\PayloadFactory, Factory\PromptFactory, PromptTypes};
use App\ValueResolver\{ObjectiveValueResolver as ObjectiveVR, StepValueResolver as StepVR};
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Response};
use Symfony\Component\HttpKernel\Attribute\ValueResolver as VR;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/objectives/{objective_id}/steps', name: 'api.objectives.steps.')]
class StepController extends AbstractController
{
    private const SERIALIZATION_GROUPS = ['common.read', 'step.read'];

    #[Route('/generate', name: "generate", methods: ['GET'])]
    public function generate(
        #[VR(ObjectiveVR::class)] Objective $objective,
        OpenAiAgent $openAi,
        ContextFactory $contextFactory,
        PromptFactory $promptFactory,
        PayloadFactory $payloadFactory,
        ObjectMapperInterface $objectMapper,
        StepRepository $stepRepository
    ): JsonResponse {
        $context = $contextFactory->handle(PromptTypes::StepsGenerate, $objective);
        $prompt = $promptFactory->handle(PromptTypes::StepsGenerate, $context);
        $result = $openAi->send($prompt);
        $payload = $payloadFactory->handle(PromptTypes::StepsGenerate, $result);

        $steps = [];

        foreach ($payload->getPayload()->steps as $payloadStep) {
            $step = $objectMapper->map($payloadStep, Step::class);
            $steps[] = $step->setObjective($objective);
        }

        $stepRepository->saveAll($steps, true);

        return $this->json($steps, Response::HTTP_OK, context: ['groups' => self::SERIALIZATION_GROUPS]);
    }

    #[Route('/{step_id}', name: "read", methods: ['GET'])]
    public function read(#[VR(ObjectiveVR::class)] Objective $objective, #[VR(StepVR::class)] Step $step): JsonResponse
    {
        if ($step->getObjective() !== $objective) {
            throw new NotFoundHttpException(
                sprintf('["%s"] Step does not belong to the specified Objective.', self::class)
            );
        }

        return $this->json($step, Response::HTTP_OK, context: ['groups' => self::SERIALIZATION_GROUPS]);
    }
}
