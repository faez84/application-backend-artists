<?php

namespace App\Repository;

use App\Entity\Album;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PhpParser\Node\Expr\Cast\Array_;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Album|null find($id, $lockMode = null, $lockVersion = null)
 * @method Album|null findOneBy(array $criteria, array $orderBy = null)
 * @method Album[]    findAll()
 * @method Album[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlbumRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Album::class);
    }

    /**
     * @param int $artistId
     * @return array
     */
    public function findByArtist(int $artistId): array
    {
        return $this->createQueryBuilder('a')
            ->select('a.token, a.title, a.cover')
            ->andWhere('a.artistId = :val')
            ->setParameter('val', $artistId)
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $token
     * @return mixed
     */
    public function findByToken(string $token):array
    {
        return $this->createQueryBuilder('a')
            ->select(
                'a.id, a.token, a.title, a.description, a.cover, art.name as artist_name, art.token as artist_token'
            )
            ->leftJoin('a.artistId', 'art')
            ->andWhere('a.token = :val')
            ->setParameter('val', $token)
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
