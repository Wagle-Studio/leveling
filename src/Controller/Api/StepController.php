<?php

namespace App\Controller\Api;

use App\Domain\ProgressionEngine\Scenario\PortalBuild\PortalOpenQueuePayloadDto;
use App\Entity\{Objective, Step};
use App\Libs\Queue\QueueManagerInterface;
use App\Libs\Queue\QueueJobEnum;
use App\ValueResolver\{ObjectiveValueResolver as ObjectiveVR, StepValueResolver as StepVR};
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Response};
use Symfony\Component\HttpKernel\Attribute\ValueResolver as VR;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/objectives/{objective_id}/steps', name: 'api.objectives.steps.')]
class StepController extends AbstractController
{
    private const SERIALIZATION_GROUPS = ['common.read', 'step.read'];

    #[Route('/generate', name: "generate", methods: ['GET'])]
    public function generate(#[VR(ObjectiveVR::class)] Objective $objective, QueueManagerInterface $queueManager): JsonResponse
    {
        $dto = new PortalOpenQueuePayloadDto();
        $dto->objective_id = $objective->getId();

        $queueManager->enqueueJob(QueueJobEnum::SCENARIO_PORTAL_BUILD, $dto);

        return $this->json([], Response::HTTP_OK, context: ['groups' => self::SERIALIZATION_GROUPS]);
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
