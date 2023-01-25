<?php

declare(strict_types=1);

namespace App\Message\Infrastructure;

use App\Message\Command\ChangeMessageStatus;
use App\Message\Command\DeleteMessage;
use App\Message\Domain\MessageRecipient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MessageRecipient>
 *
 * @method MessageRecipient|null find($id, $lockMode = null, $lockVersion = null)
 * @method MessageRecipient|null findOneBy(array $criteria, array $orderBy = null)
 * @method MessageRecipient[]    findAll()
 * @method MessageRecipient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRecipientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MessageRecipient::class);
    }

    public function save(MessageRecipient $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(MessageRecipient $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function deleteRecipient(DeleteMessage $command): void
    {
        $this->createQueryBuilder('mr')
            ->where('mr.user = :user')
            ->andWhere('mr.message = :message')
            ->setParameter('user', $command->getUser()->getId())
            ->setParameter('message', $command->getId())
            ->delete()
            ->set('mr.isRead', ':read')
            ->getQuery()
            ->execute();
    }

    public function changeStatus(ChangeMessageStatus $command): void
    {
        $this->createQueryBuilder('mr')
            ->where('mr.user = :user')
            ->andWhere('mr.message = :message')
            ->setParameter('user', $command->getUserId())
            ->setParameter('message', $command->getMessageId())
            ->update()
            ->set('mr.isRead', ':read')
            ->setParameter('read', $command->isRead())
            ->getQuery()
            ->execute();
    }
}
