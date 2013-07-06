<?php

namespace Application\Entity;
use Doctrine\ORM\Mapping as ORM;
use JerekBase\Entity\IdPropertyTrait;
use JerekBase\Entity\NamePropertyTrait;

/**
 * @ORM\Entity
 * @ORM\Table(name="row_type")
 */
class RowType {
    use IdPropertyTrait;
    use NamePropertyTrait;

    /**
     * @var Row
     * @ORM\OneToMany(targetEntity="Row", mappedBy="type")
     */
    protected $rows;

    // Getters and Setters

    /**
     * @param \Application\Entity\Row $rows
     */
    public function setRows($rows)
    {
        $this->rows = $rows;
    }

    /**
     * @return \Application\Entity\Row
     */
    public function getRows()
    {
        return $this->rows;
    }
}
