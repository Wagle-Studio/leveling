<?php

namespace App\Controller\Api;

use App\Dto\Request\{DomainCreateInput, DomainUpdateInput};
use App\Entity\Domain;
use App\Repository\DomainRepository;
use App\ValueResolver\DomainValueResolver as DomainVR;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\{MapRequestPayload as Map, ValueResolver as VR};
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/domains', name: 'api.domains.')]
class DomainController extends AbstractApiController
{
    private const SERIALIZATION_GROUPS = ['common.read', 'domain.read'];

    public function __construct(
        private DomainRepository $domainRepository,
        private ObjectMapperInterface $objectMapper
    ) {}

    #[Route('', name: "browse", methods: ['GET'])]
    public function browse(): JsonResponse
    {
        return $this->jsonResponse($this->domainRepository->findAll(), groups: self::SERIALIZATION_GROUPS);
    }

    #[Route('/{domain_id}', name: "read", methods: ['GET'])]
    public function read(#[VR(DomainVR::class)] Domain $domain): JsonResponse
    {
        return $this->jsonResponse($domain, groups: self::SERIALIZATION_GROUPS);
    }

    #[Route('/{domain_id}', name: "edit", methods: ['PATCH', 'PUT'])]
    public function edit(#[VR(DomainVR::class)] Domain $domain, #[Map] DomainUpdateInput $input): JsonResponse
    {
        $this->domainRepository->save($this->objectMapper->map($input, $domain), true);

        return $this->jsonResponse($domain, groups: self::SERIALIZATION_GROUPS);
    }

    #[Route('', name: "add", methods: ['POST'])]
    public function add(#[Map] DomainCreateInput $input): JsonResponse
    {
        $domain = $this->objectMapper->map($input, Domain::class);
        $this->domainRepository->save($domain, true);

        return $this->jsonCreated($domain, self::SERIALIZATION_GROUPS);
    }

    #[Route('/{domain_id}', name: "delete", methods: ['DELETE'])]
    public function delete(#[VR(DomainVR::class)] Domain $domain): JsonResponse
    {
        $this->domainRepository->remove($domain, true);

        return $this->jsonNoContent();
    }
}
