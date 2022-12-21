<?php

namespace App\Repository;

use App\Entity\Voucher;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Voucher>
 *
 * @method Voucher|null find($id, $lockMode = null, $lockVersion = null)
 * @method Voucher|null findOneBy(array $criteria, array $orderBy = null)
 * @method Voucher[]    findAll()
 * @method Voucher[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VoucherRepository extends ServiceEntityRepository
{
    const UNUSED_VOUCHER = 0;
    const USED_VOUCHER = 1;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Voucher::class);
    }

    public function add(Voucher $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Voucher $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneById(string $voucherId): ?Voucher
    {
        return $this->findOneBy(["id" => $voucherId]);
    }

    /**
     * @return Voucher[] Returns an array of Voucher objects
     */
    public function findActiveVouchers(): array
    {
        return $this->createQueryBuilder('v')
            ->where('v.expires_at > :expiredAt')
            ->andWhere('v.used = :used')
            ->setParameter('expiredAt', new DateTime('now'))
            ->setParameter('used', self::UNUSED_VOUCHER)
            ->orderBy('v.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Voucher[] Returns an array of Voucher objects
     */
    public function findExpiredVouchers(): array
    {
        return $this->createQueryBuilder('v')
            ->where('v.expires_at < :expiredAt OR v.used = :used')
            ->setParameter('expiredAt', new DateTime('now'))
            ->setParameter('used', self::USED_VOUCHER)
            ->orderBy('v.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getActiveVoucher($voucherId): ?Voucher
    {
        return $this->createQueryBuilder('v')
            ->where('v.id = :voucherId')
            ->andWhere('v.expires_at > :expiredAt')
            ->andWhere('v.used = :used')
            ->setParameter('voucherId', $voucherId)
            ->setParameter('expiredAt', new DateTime('now'))
            ->setParameter('used', self::UNUSED_VOUCHER)
            ->orderBy('v.id', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}
