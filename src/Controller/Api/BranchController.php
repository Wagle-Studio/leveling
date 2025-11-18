<?php

namespace App\Controller\Api;

use App\Dto\Request\BranchRequestPayload as Payload;
use App\Entity\{Branch, Domain};
use App\Repository\BranchRepository;
use App\ValueResolver\{BranchValueResolver as BranchVR, DomainValueResolver as DomainVR};
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Response};
use Symfony\Component\HttpKernel\Attribute\{MapRequestPayload as Map, ValueResolver as VR};
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/domains/{domain_id}/branches', name: 'api.domains.branches.')]
class BranchController extends AbstractController
{
    private const SERIALIZATION_GROUPS = ['common.read', 'branch.read'];

    public function __construct(
        private BranchRepository $branchRepository,
        private ObjectMapperInterface $objectMapper
    ) {}

    #[Route('', name: "browse", methods: ['GET'])]
    public function browse(#[VR(DomainVR::class)] Domain $domain): JsonResponse
    {
        return $this->json($domain->getBranches(), Response::HTTP_OK, ['groups' => self::SERIALIZATION_GROUPS]);
    }

    #[Route('/{branch_id}', name: "read", methods: ['GET'])]
    public function read(#[VR(DomainVR::class)] Domain $domain, #[VR(BranchVR::class)] Branch $branch): JsonResponse
    {
        if (!$branch->getDomains()->contains($domain)) {
            throw new NotFoundHttpException(
                sprintf('["%s"] Branch does not belong to the specified Domain.', self::class)
            );
        }

        return $this->json($branch, Response::HTTP_OK, ['groups' => self::SERIALIZATION_GROUPS]);
    }

    #[Route('/{branch_id}', name: "edit", methods: ['PATCH', 'PUT'])]
    public function edit(#[VR(DomainVR::class)] Domain $domain, #[VR(BranchVR::class)] Branch $branch, #[Map] Payload $request): JsonResponse
    {
        if (!$branch->getDomains()->contains($domain)) {
            throw new NotFoundHttpException(
                sprintf('["%s"] Branch does not belong to the specified Domain.', self::class)
            );
        }

        $this->branchRepository->save($this->objectMapper->map($request, $branch), true);

        return $this->json($branch, Response::HTTP_OK, ['groups' => self::SERIALIZATION_GROUPS]);
    }

    #[Route('', name: "add", methods: ['POST'])]
    public function add(#[VR(DomainVR::class)] Domain $domain, #[Map] Payload $request): JsonResponse
    {
        $branch = $this->objectMapper->map($request, Branch::class);
        $branch->addDomain($domain);
        $this->branchRepository->save($branch, true);

        return $this->json($branch, Response::HTTP_CREATED, ['groups' => self::SERIALIZATION_GROUPS]);
    }

    #[Route('/{branch_id}', name: "delete", methods: ['DELETE'])]
    public function delete(#[VR(DomainVR::class)] Domain $domain, #[VR(BranchVR::class)] Branch $branch): JsonResponse
    {
        if (!$branch->getDomains()->contains($domain)) {
            throw new NotFoundHttpException(
                sprintf('["%s"] Branch does not belong to the specified Domain.', self::class)
            );
        }

        $this->branchRepository->remove($branch, true);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
