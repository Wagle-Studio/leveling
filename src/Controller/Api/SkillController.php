<?php

namespace App\Controller\Api;

use App\Dto\Request\{SkillCreateInput, SkillUpdateInput};
use App\Entity\{Branch, Skill};
use App\Repository\SkillRepository;
use App\ValueResolver\{BranchValueResolver as BranchVR, SkillValueResolver as SkillVR};
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\{MapRequestPayload as Map, ValueResolver as VR};
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/branches/{branch_id}/skills', name: 'api.branches.skills.')]
class SkillController extends AbstractApiController
{
    private const SERIALIZATION_GROUPS = ['common.read', 'skill.read'];

    public function __construct(
        private SkillRepository $skillRepository,
        private ObjectMapperInterface $objectMapper
    ) {}

    #[Route('', name: "browse", methods: ['GET'])]
    public function browse(#[VR(BranchVR::class)] Branch $branch): JsonResponse
    {
        return $this->jsonResponse($branch->getSkills(), groups: self::SERIALIZATION_GROUPS);
    }

    #[Route('/{skill_id}', name: "read", methods: ['GET'])]
    public function read(#[VR(BranchVR::class)] Branch $branch, #[VR(SkillVR::class)] Skill $skill): JsonResponse
    {
        $this->assertRelation(
            $branch->getSkills()->contains($skill),
            'Skill does not belong to the specified Branch.'
        );

        return $this->jsonResponse($skill, groups: self::SERIALIZATION_GROUPS);
    }

    #[Route('/{skill_id}', name: "edit", methods: ['PATCH', 'PUT'])]
    public function edit(#[VR(BranchVR::class)] Branch $branch, #[VR(SkillVR::class)] Skill $skill, #[Map] SkillUpdateInput $input): JsonResponse
    {
        $this->assertRelation(
            $branch->getSkills()->contains($skill),
            'Skill does not belong to the specified Branch.'
        );

        $this->skillRepository->save($this->objectMapper->map($input, $skill), true);

        return $this->jsonResponse($skill, groups: self::SERIALIZATION_GROUPS);
    }

    #[Route('', name: "add", methods: ['POST'])]
    public function add(#[VR(BranchVR::class)] Branch $branch, #[Map] SkillCreateInput $input): JsonResponse
    {
        $skill = $this->objectMapper->map($input, Skill::class);
        $skill->addBranch($branch);
        $this->skillRepository->save($skill, true);

        return $this->jsonCreated($skill, self::SERIALIZATION_GROUPS);
    }

    #[Route('/{skill_id}', name: "delete", methods: ['DELETE'])]
    public function delete(#[VR(BranchVR::class)] Branch $branch, #[VR(SkillVR::class)] Skill $skill): JsonResponse
    {
        $this->assertRelation(
            $branch->getSkills()->contains($skill),
            'Skill does not belong to the specified Branch.'
        );

        $this->skillRepository->remove($skill, true);

        return $this->jsonNoContent();
    }
}
