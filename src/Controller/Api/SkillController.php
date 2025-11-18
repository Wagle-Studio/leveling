<?php

namespace App\Controller\Api;

use App\Dto\Request\SkillRequestPayload as Payload;
use App\Entity\{Branch, Skill};
use App\Repository\SkillRepository;
use App\ValueResolver\{BranchValueResolver as BranchVR, SkillValueResolver as SkillVR};
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Response};
use Symfony\Component\HttpKernel\Attribute\{MapRequestPayload as Map, ValueResolver as VR};
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/branches/{branch_id}/skills', name: 'api.branches.skills.')]
class SkillController extends AbstractController
{
    private const SERIALIZATION_GROUPS = ['common.read', 'skill.read'];

    public function __construct(
        private SkillRepository $skillRepository,
        private ObjectMapperInterface $objectMapper
    ) {}

    #[Route('', name: "browse", methods: ['GET'])]
    public function browse(#[VR(BranchVR::class)] Branch $branch): JsonResponse
    {
        return $this->json($branch->getSkills(), Response::HTTP_OK, ['groups' => self::SERIALIZATION_GROUPS]);
    }

    #[Route('/{skill_id}', name: "read", methods: ['GET'])]
    public function read(#[VR(BranchVR::class)] Branch $branch, #[VR(SkillVR::class)] Skill $skill): JsonResponse
    {
        if (!$branch->getSkills()->contains($skill)) {
            throw new NotFoundHttpException(
                sprintf('["%s"] Skill does not belong to the specified Branch.', self::class)
            );
        }

        return $this->json($skill, Response::HTTP_OK, ['groups' => self::SERIALIZATION_GROUPS]);
    }

    #[Route('/{skill_id}', name: "edit", methods: ['PATCH', 'PUT'])]
    public function edit(#[VR(BranchVR::class)] Branch $branch, #[VR(SkillVR::class)] Skill $skill, #[Map] Payload $request): JsonResponse
    {
        if (!$branch->getSkills()->contains($skill)) {
            throw new NotFoundHttpException(
                sprintf('["%s"] Skill does not belong to the specified Branch.', self::class)
            );
        }

        $this->skillRepository->save($this->objectMapper->map($request, $skill), true);

        return $this->json($skill, Response::HTTP_OK, ['groups' => self::SERIALIZATION_GROUPS]);
    }

    #[Route('', name: "add", methods: ['POST'])]
    public function add(#[VR(BranchVR::class)] Branch $branch, #[Map] Payload $request): JsonResponse
    {
        $skill = $this->objectMapper->map($request, Skill::class);
        $skill->addBranch($branch);
        $this->skillRepository->save($skill, true);

        return $this->json($skill, Response::HTTP_CREATED, ['groups' => self::SERIALIZATION_GROUPS]);
    }

    #[Route('/{skill_id}', name: "delete", methods: ['DELETE'])]
    public function delete(#[VR(BranchVR::class)] Branch $branch, #[VR(SkillVR::class)] Skill $skill): JsonResponse
    {
        if (!$branch->getSkills()->contains($skill)) {
            throw new NotFoundHttpException(
                sprintf('["%s"] Skill does not belong to the specified Branch.', self::class)
            );
        }

        $this->skillRepository->remove($skill, true);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
