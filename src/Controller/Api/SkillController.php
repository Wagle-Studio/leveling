<?php

namespace App\Controller\Api;

use App\Dto\SkillRequestPayload;
use App\Entity\Branch;
use App\Entity\Skill;
use App\Repository\SkillRepository;
use App\ValueResolver\BranchValueResolver;
use App\ValueResolver\SkillValueResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
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
    public function browse(#[ValueResolver(BranchValueResolver::class)] Branch $branch): JsonResponse
    {
        $skills = $branch->getSkills();

        return $this->json(
            data: $skills,
            status: Response::HTTP_OK,
            context: ['groups' => self::SERIALIZATION_GROUPS]
        );
    }

    #[Route('/{skill_id}', name: "read", methods: ['GET'])]
    public function read(
        #[ValueResolver(BranchValueResolver::class)] Branch $branch,
        #[ValueResolver(SkillValueResolver::class)] Skill $skill
    ): JsonResponse {
        if (!$branch->getSkills()->contains($skill)) {
            throw new NotFoundHttpException('La compétence n\'appartient pas à la branche spécifiée.');
        }

        return $this->json(
            data: $skill,
            status: Response::HTTP_OK,
            context: ['groups' => self::SERIALIZATION_GROUPS]
        );
    }

    #[Route('/{skill_id}', name: "edit", methods: ['PATCH', 'PUT'])]
    public function edit(
        #[ValueResolver(BranchValueResolver::class)] Branch $branch,
        #[ValueResolver(SkillValueResolver::class)] Skill $skill,
        #[MapRequestPayload] SkillRequestPayload $request
    ): JsonResponse {
        if (!$branch->getSkills()->contains($skill)) {
            throw new NotFoundHttpException('La compétence n\'appartient pas à la branche spécifiée.');
        }

        $skill = $this->objectMapper->map($request, $skill);
        $this->skillRepository->save($skill, true);

        return $this->json(
            data: $skill,
            status: Response::HTTP_OK,
            context: ['groups' => self::SERIALIZATION_GROUPS]
        );
    }

    #[Route('', name: "add", methods: ['POST'])]
    public function add(
        #[ValueResolver(BranchValueResolver::class)] Branch $branch,
        #[MapRequestPayload] SkillRequestPayload $request
    ): JsonResponse {
        $skill = $this->objectMapper->map($request, Skill::class);
        $skill->addBranch($branch);
        $this->skillRepository->save($skill, true);

        return $this->json(
            data: $skill,
            status: Response::HTTP_CREATED,
            context: ['groups' => self::SERIALIZATION_GROUPS]
        );
    }

    #[Route('/{skill_id}', name: "delete", methods: ['DELETE'])]
    public function delete(
        #[ValueResolver(BranchValueResolver::class)] Branch $branch,
        #[ValueResolver(SkillValueResolver::class)] Skill $skill,
    ): JsonResponse {
        if (!$branch->getSkills()->contains($skill)) {
            throw new NotFoundHttpException('La compétence n\'appartient pas à la branche spécifiée.');
        }

        $this->skillRepository->remove($skill, true);

        return $this->json(data: null, status: Response::HTTP_NO_CONTENT);
    }
}
