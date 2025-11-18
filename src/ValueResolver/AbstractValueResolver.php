<?php

namespace App\ValueResolver;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class AbstractValueResolver implements ValueResolverInterface
{
    protected string $entityName;
    protected string $entityShortName;
    protected string $entityField;
    protected EntityRepository $entityRepository;
    protected $validateFieldFn;

    protected function __construct(
        string $entityName,
        string $entityField,
        EntityRepository $entityRepository,
        callable $validateFieldFn
    ) {
        $this->entityName = $entityName;
        $this->entityShortName = (new \ReflectionClass($entityName))->getShortName();
        $this->entityField = $entityField;
        $this->entityRepository = $entityRepository;
        $this->validateFieldFn = $validateFieldFn;
    }

    abstract protected function validateFieldFn(mixed $entityIdentifier): bool;

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== $this->entityName) {
            return [];
        }

        $entityIdentifier = $this->getEntityIdentifier($request);

        if (!$entityIdentifier) {
            throw new BadRequestHttpException(
                sprintf('["%s"] %s %s is required.', self::class, $this->entityShortName, $this->entityField)
            );
        }

        if (!($this->validateFieldFn)($entityIdentifier)) {
            throw new BadRequestHttpException(
                sprintf('["%s"] %s %s is invalid.', self::class, $this->entityShortName, $this->entityField)
            );
        }

        $entity = $this->entityRepository->findOneBy([$this->entityField => $entityIdentifier]);

        if (!$entity) {
            throw new NotFoundHttpException(
                sprintf('["%s"] %s not found.', self::class, $this->entityShortName)
            );
        }

        return [$entity];
    }

    private function getEntityIdentifier(Request $request): mixed
    {
        $needle = strtolower("{$this->entityShortName}_{$this->entityField}");

        if ($request->attributes->has($needle)) {
            $value = $request->attributes->get($needle);
            return is_numeric($value) ? (int) $value : $value;
        }

        $data = json_decode($request->getContent(), true);

        if (is_array($data) && isset($data[$needle])) {
            $value = $data[$needle];
            return is_numeric($value) ? (int) $value : $value;
        }

        return null;
    }
}
