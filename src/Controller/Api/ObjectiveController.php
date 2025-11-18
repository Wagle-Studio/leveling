<?php

namespace App\Controller\Api;

use App\Dto\Request\ObjectiveRequestPayload as Payload;
use App\Entity\{Objective, Skill};
use App\Repository\ObjectiveRepository;
use App\ValueResolver\{ObjectiveValueResolver as ObjectiveVR, SkillValueResolver as SkillVR};
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Response};
use Symfony\Component\HttpKernel\Attribute\{MapRequestPayload as Map, ValueResolver as VR};
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/skills/{skill_id}/objectives', name: 'api.skills.objectives.')]
class ObjectiveController extends AbstractController
{
    private const SERIALIZATION_GROUPS = ['common.read', 'objective.read'];

    public function __construct(
        private ObjectiveRepository $objectiveRepository,
        private ObjectMapperInterface $objectMapper
    ) {}

    #[Route('', name: "browse", methods: ['GET'])]
    public function browse(#[VR(SkillVR::class)] Skill $skill): JsonResponse
    {
        return $this->json($skill->getObjectives(), Response::HTTP_OK, ['groups' => self::SERIALIZATION_GROUPS]);
    }

    #[Route('/{objective_id}', name: "read", methods: ['GET'])]
    public function read(#[VR(SkillVR::class)] Skill $skill, #[VR(ObjectiveVR::class)] Objective $objective): JsonResponse
    {
        if ($objective->getSkill() !== $skill) {
            throw new NotFoundHttpException(
                sprintf('["%s"] Objective does not belong to the specified Skill.', self::class)
            );
        }

        return $this->json($objective, Response::HTTP_OK, ['groups' => self::SERIALIZATION_GROUPS]);
    }

    #[Route('/{objective_id}', name: "edit", methods: ['PATCH', 'PUT'])]
    public function edit(#[VR(SkillVR::class)] Skill $skill, #[VR(ObjectiveVR::class)] Objective $objective, #[Map] Payload $request): JsonResponse
    {
        if ($objective->getSkill() !== $skill) {
            throw new NotFoundHttpException(
                sprintf('["%s"] Objective does not belong to the specified Skill.', self::class)
            );
        }

        $this->objectiveRepository->save($this->objectMapper->map($request, $objective), true);

        return $this->json($objective, Response::HTTP_OK, ['groups' => self::SERIALIZATION_GROUPS]);
    }

    #[Route('', name: "add", methods: ['POST'])]
    public function add(#[VR(SkillVR::class)] Skill $skill, #[Map] Payload $request): JsonResponse
    {
        $objective = $this->objectMapper->map($request, Objective::class);
        $objective->setSkill($skill);
        $this->objectiveRepository->save($objective, true);

        return $this->json($objective, Response::HTTP_CREATED, ['groups' => self::SERIALIZATION_GROUPS]);
    }

    #[Route('/{objective_id}', name: "delete", methods: ['DELETE'])]
    public function delete(#[VR(SkillVR::class)] Skill $skill, #[VR(ObjectiveVR::class)] Objective $objective): JsonResponse
    {
        if ($objective->getSkill() !== $skill) {
            throw new NotFoundHttpException(
                sprintf('["%s"] Objective does not belong to the specified Skill.', self::class)
            );
        }

        $this->objectiveRepository->remove($objective, true);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
