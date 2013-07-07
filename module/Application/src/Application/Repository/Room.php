<?php

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

/**
 * Class Room
 * @package Application\Repository
 */
class Room extends EntityRepository
{
    public function getRoomData($roomId)
    {
        $qb = $this->createQueryBuilder('room');
        $qb->select('
                room,
                row,
                partial user.{id,displayName},
                type,
                rolls,
                message
            ')
            ->leftJoin('room.rows', 'row')
            ->leftJoin('row.user', 'user')
            ->leftJoin('row.type', 'type')
            ->leftJoin('row.rolls', 'rolls')
            ->leftJoin('row.message', 'message')
            ->orderBy('row.time', 'ASC')
            ->where('room.id = ?1')
            ->setParameter(1, $roomId);

        $query = $qb->getQuery();
        $query->useResultCache(true);

        $result = $query->execute([], Query::HYDRATE_ARRAY);

        return $result[0];
    }
}
