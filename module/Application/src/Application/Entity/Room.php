<?php

namespace Application\Entity;
use Doctrine\ORM\Mapping as ORM;
use JerekBase\Entity\IdPropertyTrait;
use JerekBase\Entity\NamePropertyTrait;
use JerekBase\Entity\UserPropertyTrait;

/**
 * @ORM\Entity
 * @ORM\Table(name="room")
 */
class Room {
    use IdPropertyTrait;
    use NamePropertyTrait;
    use UserPropertyTrait;

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    protected $deleted = false;

    /**
     * @var Row
     * @ORM\OneToMany(targetEntity="Row", mappedBy="room")
     */
    protected $rows;

    /**
     * @param boolean $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @return boolean
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @return \Application\Entity\Row
     */
    public function getRows()
    {
        return $this->rows;
    }

}
