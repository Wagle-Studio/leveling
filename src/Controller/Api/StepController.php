<?php

namespace App\Controller\Api;

use App\Dto\Request\{StepCreateInput, StepUpdateInput};
use App\Entity\{Objective, Step};
use App\Repository\StepRepository;
use App\ValueResolver\{ObjectiveValueResolver as ObjectiveVR, StepValueResolver as StepVR};
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\{MapRequestPayload as Map, ValueResolver as VR};
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/objectives/{objective_id}/steps', name: 'api.objectives.steps.')]
class StepController extends AbstractApiController
{
    private const SERIALIZATION_GROUPS = ['common.read', 'step.read'];

    public function __construct(
        private StepRepository $stepRepository,
        private ObjectMapperInterface $objectMapper
    ) {}

    #[Route('', name: "browse", methods: ['GET'])]
    public function browse(#[VR(ObjectiveVR::class)] Objective $objective): JsonResponse
    {
        return $this->jsonResponse($objective->getSteps(), groups: self::SERIALIZATION_GROUPS);
    }

    #[Route('/{step_id}', name: "read", methods: ['GET'])]
    public function read(#[VR(ObjectiveVR::class)] Objective $objective, #[VR(StepVR::class)] Step $step): JsonResponse
    {
        $this->assertRelation(
            $objective->getSteps()->contains($step),
            'Step does not belong to the specified Objective.'
        );

        return $this->jsonResponse($step, groups: self::SERIALIZATION_GROUPS);
    }

    #[Route('/{step_id}', name: "edit", methods: ['PATCH', 'PUT'])]
    public function edit(#[VR(ObjectiveVR::class)] Objective $objective, #[VR(StepVR::class)] Step $step, #[Map] StepUpdateInput $input): JsonResponse
    {
        $this->assertRelation(
            $objective->getSteps()->contains($step),
            'Step does not belong to the specified Objective.'
        );

        $this->stepRepository->save($this->objectMapper->map($input, $step), true);

        return $this->jsonResponse($step, groups: self::SERIALIZATION_GROUPS);
    }

    #[Route('', name: "add", methods: ['POST'])]
    public function add(#[VR(ObjectiveVR::class)] Objective $objective, #[Map] StepCreateInput $input): JsonResponse
    {
        $step = $this->objectMapper->map($input, Step::class);
        $step->setObjective($objective);
        $this->stepRepository->save($step, true);

        return $this->jsonCreated($step, self::SERIALIZATION_GROUPS);
    }

    #[Route('/{step_id}', name: "delete", methods: ['DELETE'])]
    public function delete(#[VR(ObjectiveVR::class)] Objective $objective, #[VR(StepVR::class)] Step $step): JsonResponse
    {
        $this->assertRelation(
            $objective->getSteps()->contains($step),
            'Step does not belong to the specified Objective.'
        );

        $this->stepRepository->remove($step, true);

        return $this->jsonNoContent();
    }
}
