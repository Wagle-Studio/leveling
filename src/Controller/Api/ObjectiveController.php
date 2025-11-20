<?php

namespace App\Controller\Api;

use App\Dto\Request\ObjectiveCreateInput;
use App\Dto\Request\ObjectiveUpdateInput;
use App\Entity\{Objective, Skill};
use App\Repository\ObjectiveRepository;
use App\ValueResolver\{ObjectiveValueResolver as ObjectiveVR, SkillValueResolver as SkillVR};
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\{MapRequestPayload as Map, ValueResolver as VR};
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/skills/{skill_id}/objectives', name: 'api.skills.objectives.')]
class ObjectiveController extends AbstractApiController
{
    private const SERIALIZATION_GROUPS = ['common.read', 'objective.read'];

    public function __construct(
        private ObjectiveRepository $objectiveRepository,
        private ObjectMapperInterface $objectMapper
    ) {}

    #[Route('', name: "browse", methods: ['GET'])]
    public function browse(#[VR(SkillVR::class)] Skill $skill): JsonResponse
    {
        return $this->jsonResponse($skill->getObjectives(), groups: self::SERIALIZATION_GROUPS);
    }

    #[Route('/{objective_id}', name: "read", methods: ['GET'])]
    public function read(#[VR(SkillVR::class)] Skill $skill, #[VR(ObjectiveVR::class)] Objective $objective): JsonResponse
    {
        $this->assertRelation(
            $skill->getObjectives()->contains($objective),
            'Objective does not belong to the specified Skill.'
        );

        return $this->jsonResponse($objective, groups: self::SERIALIZATION_GROUPS);
    }

    #[Route('/{objective_id}', name: "edit", methods: ['PATCH', 'PUT'])]
    public function edit(#[VR(SkillVR::class)] Skill $skill, #[VR(ObjectiveVR::class)] Objective $objective, #[Map] ObjectiveUpdateInput $input): JsonResponse
    {
        $this->assertRelation(
            $skill->getObjectives()->contains($objective),
            'Objective does not belong to the specified Skill.'
        );

        $this->objectiveRepository->save($this->objectMapper->map($input, $objective), true);

        return $this->jsonResponse($objective, groups: self::SERIALIZATION_GROUPS);
    }

    #[Route('', name: "add", methods: ['POST'])]
    public function add(#[VR(SkillVR::class)] Skill $skill, #[Map] ObjectiveCreateInput $input): JsonResponse
    {
        $objective = $this->objectMapper->map($input, Objective::class);
        $objective->setSkill($skill);
        $this->objectiveRepository->save($objective, true);

        return $this->jsonCreated($objective, self::SERIALIZATION_GROUPS);
    }

    #[Route('/{objective_id}', name: "delete", methods: ['DELETE'])]
    public function delete(#[VR(SkillVR::class)] Skill $skill, #[VR(ObjectiveVR::class)] Objective $objective): JsonResponse
    {
        $this->assertRelation(
            $skill->getObjectives()->contains($objective),
            'Objective does not belong to the specified Skill.'
        );

        $this->objectiveRepository->remove($objective, true);

        return $this->jsonNoContent();
    }
}
