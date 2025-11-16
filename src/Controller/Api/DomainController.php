<?php

namespace App\Controller\Api;

use App\Dto\DomainRequestPayload;
use App\Entity\Domain;
use App\Repository\DomainRepository;
use App\ValueResolver\DomainValueResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
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
        return $this->json(
            data: $this->domainRepository->findAll(),
            status: Response::HTTP_OK,
            context: ['groups' => self::SERIALIZATION_GROUPS]
        );
    }

    #[Route('/{domain_id}', name: "read", methods: ['GET'])]
    public function read(#[ValueResolver(DomainValueResolver::class)] Domain $domain): JsonResponse
    {
        return $this->json(
            data: $domain,
            status: Response::HTTP_OK,
            context: ['groups' => self::SERIALIZATION_GROUPS]
        );
    }

    #[Route('/{domain_id}', name: "edit", methods: ['PATCH', 'PUT'])]
    public function edit(
        #[ValueResolver(DomainValueResolver::class)] Domain $domain,
        #[MapRequestPayload] DomainRequestPayload $request
    ): JsonResponse {
        $domain = $this->objectMapper->map($request, $domain);
        $this->domainRepository->save($domain, true);

        return $this->json(
            data: $domain,
            status: Response::HTTP_OK,
            context: ['groups' => self::SERIALIZATION_GROUPS]
        );
    }

    #[Route('', name: "add", methods: ['POST'])]
    public function add(#[MapRequestPayload] DomainRequestPayload $request,): JsonResponse
    {
        $domain = $this->objectMapper->map($request, Domain::class);
        $this->domainRepository->save($domain, true);

        return $this->json(
            data: $domain,
            status: Response::HTTP_CREATED,
            context: ['groups' => self::SERIALIZATION_GROUPS]
        );
    }

    #[Route('/{domain_id}', name: "delete", methods: ['DELETE'])]
    public function delete(#[ValueResolver(DomainValueResolver::class)] Domain $domain): JsonResponse
    {
        $this->domainRepository->remove($domain, true);

        return $this->json(data: null, status: Response::HTTP_NO_CONTENT);
    }
}
