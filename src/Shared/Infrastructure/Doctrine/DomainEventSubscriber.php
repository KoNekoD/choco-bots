<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Doctrine;

use App\Shared\Application\Event\EventBusInterface;
use App\Shared\Domain\Entity\Aggregate;
use App\Shared\Domain\Specification\SpecificationInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PostLoadEventArgs as PL;
use Doctrine\ORM\Event\PostPersistEventArgs as PP;
use Doctrine\ORM\Event\PostRemoveEventArgs as PR;
use Doctrine\ORM\Event\PostUpdateEventArgs as PU;
use Doctrine\ORM\Events;
use ReflectionClass;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class DomainEventSubscriber
    implements EventSubscriber
{
    /**
     * @var Aggregate[]
     */
    private array $entities = [];

    public function __construct(
        private readonly EventBusInterface $eventBus,
        private readonly ContainerInterface $container
    ) {}

    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postUpdate,
            Events::postRemove,
            Events::postFlush,
            Events::postLoad,
        ];
    }

    public function postPersist(PP $args): void
    {
        $this->keepAggregateRoots($args);
    }

    private function keepAggregateRoots(PP|PU|PR|PL $args): void
    {
        $entity = $args->getObject();

        if (!($entity instanceof Aggregate)) {
            return;
        }

        $this->entities[] = $entity;
    }

    public function postUpdate(PU $args): void
    {
        $this->keepAggregateRoots($args);
    }

    public function postRemove(PR $args): void
    {
        $this->keepAggregateRoots($args);
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        if ($args->getObjectManager()->getConnection()->isTransactionActive()) {
            return;
        }

        foreach ($this->entities as $entity) {
            foreach ($entity->popEvents() as $event) {
                $this->eventBus->execute($event);
            }
        }
    }

    public function postLoad(PL $args): void
    {
        $this->keepAggregateRoots($args);

        // initialize specifications
        $entity = $args->getObject();

        $reflect = new ReflectionClass($entity);

        foreach ($reflect->getProperties() as $property) {
            $type = $property->getType();
            if ($type === null) {
                continue;
            }

            $isBuiltinMethodExist = method_exists(
                $type,
                'isBuiltin'
            );
            $isBuiltin = $isBuiltinMethodExist && $type->isBuiltin();
            if ($isBuiltin || $property->isInitialized($entity)) {
                continue;
            }

            $getNameMethodExist = method_exists(
                $type,
                'getName'
            );
            if (!$getNameMethodExist) {
                continue;
            }

            $typeName = $type->getName();

            $interfaces = class_implements($typeName);
            if (isset($interfaces[SpecificationInterface::class])) {
                $property->setValue(
                    $entity,
                    $this->container->get($typeName)
                );
            }


        }
    }
}
