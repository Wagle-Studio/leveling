<?php

namespace App\Controller\Api;

use App\Dto\ObjectiveRequestPayload;
use App\Entity\Objective;
use App\Entity\Skill;
use App\Repository\ObjectiveRepository;
use App\ValueResolver\ObjectiveValueResolver;
use App\ValueResolver\SkillValueResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
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
    public function browse(#[ValueResolver(SkillValueResolver::class)] Skill $skill): JsonResponse
    {
        $objectives = $skill->getObjectives();

        return $this->json(
            data: $objectives,
            status: Response::HTTP_OK,
            context: ['groups' => self::SERIALIZATION_GROUPS]
        );
    }

    #[Route('/{objective_id}', name: "read", methods: ['GET'])]
    public function read(
        #[ValueResolver(SkillValueResolver::class)] Skill $skill,
        #[ValueResolver(ObjectiveValueResolver::class)] Objective $objective
    ): JsonResponse {
        if ($objective->getSkill() !== $skill) {
            throw new NotFoundHttpException('L\'objectif n\'appartient pas à la compétence spécifiée.');
        }

        return $this->json(
            data: $objective,
            status: Response::HTTP_OK,
            context: ['groups' => self::SERIALIZATION_GROUPS]
        );
    }

    #[Route('/{objective_id}', name: "edit", methods: ['PATCH', 'PUT'])]
    public function edit(
        #[ValueResolver(SkillValueResolver::class)] Skill $skill,
        #[ValueResolver(ObjectiveValueResolver::class)] Objective $objective,
        #[MapRequestPayload] ObjectiveRequestPayload $request
    ): JsonResponse {
        if ($objective->getSkill() !== $skill) {
            throw new NotFoundHttpException('L\'objectif n\'appartient pas à la compétence spécifiée.');
        }

        $objective = $this->objectMapper->map($request, $objective);
        $this->objectiveRepository->save($objective, true);

        return $this->json(
            data: $objective,
            status: Response::HTTP_OK,
            context: ['groups' => self::SERIALIZATION_GROUPS]
        );
    }

    #[Route('', name: "add", methods: ['POST'])]
    public function add(
        #[ValueResolver(SkillValueResolver::class)] Skill $skill,
        #[MapRequestPayload] ObjectiveRequestPayload $request
    ): JsonResponse {
        $objective = $this->objectMapper->map($request, Objective::class);
        $objective->setSkill($skill);
        $this->objectiveRepository->save($objective, true);

        return $this->json(
            data: $objective,
            status: Response::HTTP_CREATED,
            context: ['groups' => self::SERIALIZATION_GROUPS]
        );
    }

    #[Route('/{objective_id}', name: "delete", methods: ['DELETE'])]
    public function delete(
        #[ValueResolver(SkillValueResolver::class)] Skill $skill,
        #[ValueResolver(ObjectiveValueResolver::class)] Objective $objective,
    ): JsonResponse {
        if ($objective->getSkill() !== $skill) {
            throw new NotFoundHttpException('L\'objectif n\'appartient pas à la compétence spécifiée.');
        }

        $this->objectiveRepository->remove($objective, true);

        return $this->json(data: null, status: Response::HTTP_NO_CONTENT);
    }
}
