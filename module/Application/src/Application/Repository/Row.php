<?php

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

/**
 * Class Row
 * @package Application\Repository
 */
class Row extends EntityRepository
{
    public function getRowData($rowId)
    {
        $qb = $this->createQueryBuilder('row');
        $qb->select('
                row,
                partial user.{id,displayName},
                type,
                rolls,
                message
            ')
            ->leftJoin('row.user', 'user')
            ->leftJoin('row.type', 'type')
            ->leftJoin('row.rolls', 'rolls')
            ->leftJoin('row.message', 'message')
            ->orderBy('row.time', 'ASC')
            ->orderBy('rolls.id', 'ASC')
            ->where('row.id = ?1')
            ->setParameter(1, $rowId);

        $query = $qb->getQuery();
        $query->useResultCache(false); // TODO

        $result = $query->execute([], Query::HYDRATE_ARRAY);

        return $result[0];
    }
}
