<?php

namespace App\Controller\Api;

use App\Dto\Request\{BranchCreateInput, BranchUpdateInput};
use App\Entity\{Branch, Domain};
use App\Repository\BranchRepository;
use App\ValueResolver\{BranchValueResolver as BranchVR, DomainValueResolver as DomainVR};
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\{MapRequestPayload as Map, ValueResolver as VR};
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/domains/{domain_id}/branches', name: 'api.domains.branches.')]
class BranchController extends AbstractApiController
{
    private const SERIALIZATION_GROUPS = ['common.read', 'branch.read'];

    public function __construct(
        private BranchRepository $branchRepository,
        private ObjectMapperInterface $objectMapper
    ) {}

    #[Route('', name: "browse", methods: ['GET'])]
    public function browse(#[VR(DomainVR::class)] Domain $domain): JsonResponse
    {
        return $this->jsonResponse($domain->getBranches(), groups: self::SERIALIZATION_GROUPS);
    }

    #[Route('/{branch_id}', name: "read", methods: ['GET'])]
    public function read(#[VR(DomainVR::class)] Domain $domain, #[VR(BranchVR::class)] Branch $branch): JsonResponse
    {
        $this->assertRelation(
            $branch->getDomains()->contains($domain),
            'Branch does not belong to the specified Domain.'
        );

        return $this->jsonResponse($branch, groups: self::SERIALIZATION_GROUPS);
    }

    #[Route('/{branch_id}', name: "edit", methods: ['PATCH', 'PUT'])]
    public function edit(#[VR(DomainVR::class)] Domain $domain, #[VR(BranchVR::class)] Branch $branch, #[Map] BranchUpdateInput $input): JsonResponse
    {
        $this->assertRelation(
            $branch->getDomains()->contains($domain),
            'Branch does not belong to the specified Domain.'
        );

        $this->branchRepository->save($this->objectMapper->map($input, $branch), true);

        return $this->jsonResponse($branch, groups: self::SERIALIZATION_GROUPS);
    }

    #[Route('', name: "add", methods: ['POST'])]
    public function add(#[VR(DomainVR::class)] Domain $domain, #[Map] BranchCreateInput $input): JsonResponse
    {
        $branch = $this->objectMapper->map($input, Branch::class);
        $branch->addDomain($domain);
        $this->branchRepository->save($branch, true);

        return $this->jsonCreated($branch, self::SERIALIZATION_GROUPS);
    }

    #[Route('/{branch_id}', name: "delete", methods: ['DELETE'])]
    public function delete(#[VR(DomainVR::class)] Domain $domain, #[VR(BranchVR::class)] Branch $branch): JsonResponse
    {
        $this->assertRelation(
            $branch->getDomains()->contains($domain),
            'Branch does not belong to the specified Domain.'
        );

        $this->branchRepository->remove($branch, true);

        return $this->jsonNoContent();
    }
}
