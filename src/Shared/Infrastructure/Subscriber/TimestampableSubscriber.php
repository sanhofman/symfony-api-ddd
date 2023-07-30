<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Subscriber;

use App\Shared\Domain\Model\TimestampableInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Persistence\Event\LoadClassMetadataEventArgs;

class TimestampableSubscriber implements EventSubscriber
{
    public const CREATED_FIELD = 'created';

    public const CHANGED_FIELD = 'changed';

    public function getSubscribedEvents(): array
    {
        return [
            Events::loadClassMetadata,
            Events::preUpdate,
            Events::prePersist,
        ];
    }

    /**
     * Load the class data, mapping timestamp fields to datetime fields.
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $event): void
    {
        /** @var ClassMetadataInfo $metadata */
        $metadata = $event->getClassMetadata();
        $reflection = $metadata->getReflectionClass();

        if (null !== $reflection && $reflection->implementsInterface('App\Shared\Domain\Model\TimestampableInterface')) {
            if (!$metadata->hasField(self::CREATED_FIELD)) {
                $metadata->mapField([
                    'fieldName' => self::CREATED_FIELD,
                    'type' => 'datetime',
                    'nullable' => false,
                    'columnDefinition' => 'timestamp(6)',
                ]);
            }

            if (!$metadata->hasField(self::CHANGED_FIELD)) {
                $metadata->mapField([
                    'fieldName' => self::CHANGED_FIELD,
                    'type' => 'datetime',
                    'nullable' => false,
                    'columnDefinition' => 'timestamp(6)',
                ]);
            }
        }
    }

    /**
     * Set the timestamps before update.
     */
    public function preUpdate(LifecycleEventArgs $event): void
    {
        $this->handleTimestamp($event);
    }

    /**
     * Set the timestamps before creation.
     */
    public function prePersist(LifecycleEventArgs $event): void
    {
        $this->handleTimestamp($event);
    }

    /**
     * Set the timestamps.
     *
     * Created: set when null.
     * Changed: always set/update.
     */
    private function handleTimestamp(LifecycleEventArgs $event): void
    {
        $entity = $event->getObject();

        if (!$entity instanceof TimestampableInterface) {
            return;
        }

        /** @var ClassMetadataInfo $meta */
        $meta = $event->getObjectManager()->getClassMetadata(\get_class($entity));

        // Set created.
        $created = $meta->getFieldValue($entity, self::CREATED_FIELD);
        if (null === $created) {
            $meta->setFieldValue($entity, self::CREATED_FIELD, new \DateTime());
        }

        // Set changed.
        $meta->setFieldValue($entity, self::CHANGED_FIELD, new \DateTime());
    }
}
