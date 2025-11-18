<?php

namespace App\Controller\Api;

use App\Dto\Request\DomainRequestPayload as Payload;
use App\Entity\Domain;
use App\Repository\DomainRepository;
use App\ValueResolver\DomainValueResolver as DomainVR;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Response};
use Symfony\Component\HttpKernel\Attribute\{MapRequestPayload as Map, ValueResolver as VR};
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/domains', name: 'api.domains.')]
class DomainController extends AbstractController
{
    private const SERIALIZATION_GROUPS = ['common.read', 'domain.read'];

    public function __construct(
        private DomainRepository $domainRepository,
        private ObjectMapperInterface $objectMapper
    ) {}

    #[Route('', name: "browse", methods: ['GET'])]
    public function browse(): JsonResponse
    {
        return $this->json($this->domainRepository->findAll(), Response::HTTP_OK, ['groups' => self::SERIALIZATION_GROUPS]);
    }

    #[Route('/{domain_id}', name: "read", methods: ['GET'])]
    public function read(#[VR(DomainVR::class)] Domain $domain): JsonResponse
    {
        return $this->json($domain, Response::HTTP_OK, ['groups' => self::SERIALIZATION_GROUPS]);
    }

    #[Route('/{domain_id}', name: "edit", methods: ['PATCH', 'PUT'])]
    public function edit(#[VR(DomainVR::class)] Domain $domain, #[Map] Payload $request): JsonResponse
    {
        $this->domainRepository->save($this->objectMapper->map($request, $domain), true);

        return $this->json($domain, Response::HTTP_OK, ['groups' => self::SERIALIZATION_GROUPS]);
    }

    #[Route('', name: "add", methods: ['POST'])]
    public function add(#[Map] Payload $request): JsonResponse
    {
        $domain = $this->objectMapper->map($request, Domain::class);
        $this->domainRepository->save($domain, true);

        return $this->json($domain, Response::HTTP_CREATED, ['groups' => self::SERIALIZATION_GROUPS]);
    }

    #[Route('/{domain_id}', name: "delete", methods: ['DELETE'])]
    public function delete(#[VR(DomainVR::class)] Domain $domain): JsonResponse
    {
        $this->domainRepository->remove($domain, true);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
