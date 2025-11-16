<?php

namespace App\Controller\Api;

use App\Dto\BranchRequestPayload;
use App\Entity\Branch;
use App\Entity\Domain;
use App\Repository\BranchRepository;
use App\ValueResolver\BranchValueResolver;
use App\ValueResolver\DomainValueResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
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
    public function browse(#[ValueResolver(DomainValueResolver::class)] Domain $domain): JsonResponse
    {
        $branches = $domain->getBranches();

        return $this->json(
            data: $branches,
            status: Response::HTTP_OK,
            context: ['groups' => self::SERIALIZATION_GROUPS]
        );
    }

    #[Route('/{branch_id}', name: "read", methods: ['GET'])]
    public function read(
        #[ValueResolver(DomainValueResolver::class)] Domain $domain,
        #[ValueResolver(BranchValueResolver::class)] Branch $branch
    ): JsonResponse {
        if (!$branch->getDomains()->contains($domain)) {
            throw new NotFoundHttpException('La branche n\'appartient pas au domaine spécifié.');
        }

        return $this->json(
            data: $branch,
            status: Response::HTTP_OK,
            context: ['groups' => self::SERIALIZATION_GROUPS]
        );
    }

    #[Route('/{branch_id}', name: "edit", methods: ['PATCH', 'PUT'])]
    public function edit(
        #[ValueResolver(DomainValueResolver::class)] Domain $domain,
        #[ValueResolver(BranchValueResolver::class)] Branch $branch,
        #[MapRequestPayload] BranchRequestPayload $request
    ): JsonResponse {
        if (!$branch->getDomains()->contains($domain)) {
            throw new NotFoundHttpException('La branche n\'appartient pas au domaine spécifié.');
        }

        $branch = $this->objectMapper->map($request, $branch);
        $this->branchRepository->save($branch, true);

        return $this->json(
            data: $branch,
            status: Response::HTTP_OK,
            context: ['groups' => self::SERIALIZATION_GROUPS]
        );
    }

    #[Route('', name: "add", methods: ['POST'])]
    public function add(
        #[ValueResolver(DomainValueResolver::class)] Domain $domain,
        #[MapRequestPayload] BranchRequestPayload $request
    ): JsonResponse {
        $branch = $this->objectMapper->map($request, Branch::class);
        $branch->addDomain($domain);
        $this->branchRepository->save($branch, true);

        return $this->json(
            data: $branch,
            status: Response::HTTP_CREATED,
            context: ['groups' => self::SERIALIZATION_GROUPS]
        );
    }

    #[Route('/{branch_id}', name: "delete", methods: ['DELETE'])]
    public function delete(
        #[ValueResolver(DomainValueResolver::class)] Domain $domain,
        #[ValueResolver(BranchValueResolver::class)] Branch $branch,
    ): JsonResponse {
        if (!$branch->getDomains()->contains($domain)) {
            throw new NotFoundHttpException('La branche n\'appartient pas au domaine spécifié.');
        }

        $this->branchRepository->remove($branch, true);

        return $this->json(data: null, status: Response::HTTP_NO_CONTENT);
    }
}
