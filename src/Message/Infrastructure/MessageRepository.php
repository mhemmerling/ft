<?php

declare(strict_types=1);

namespace App\Message\Infrastructure;

use App\Message\Command\DeleteMessage;
use App\Message\Domain\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 *
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function save(Message $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Message $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function listForUser(int $userId): array
    {
        return $this->createQueryBuilder('m')
            ->join('m.messageRecipients', 'mr', 'WITH', 'mr.user = :user')
            ->setParameter('user', $userId)
            ->orderBy('m.id', 'DESC')
            ->setMaxResults(100)
            ->getQuery()
            ->getResult();
    }

    public function listSentMessages(int $userId): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.sender = :user')
            ->setParameter('user', $userId)
            ->orderBy('m.id', 'DESC')
            ->setMaxResults(100)
            ->getQuery()
            ->getResult();
    }

    public function delete(DeleteMessage $command): void
    {
        $this->createQueryBuilder('m')
            ->where('m.id = :id')
            ->andWhere('m.sender = :user')
            ->setParameter('id', $command->getId())
            ->setParameter('user', $command->getUser()->getId())
            ->delete()
            ->getQuery()
            ->execute();
    }
}
