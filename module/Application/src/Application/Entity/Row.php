<?php

namespace Application\Entity;
use Doctrine\ORM\Mapping as ORM;
use JerekBase\Entity\IdPropertyTrait;
use JerekBase\Entity\UserPropertyTrait;

/**
 * @ORM\Entity
 * @ORM\Table(name="row")
 */
class Row {
    use IdPropertyTrait;
    use UserPropertyTrait;

    /**
     * @var int
     * @ORM\Column(type="datetime")
     */
    protected $time;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $typeId;

    /**
     * @var Room
     * @ORM\ManyToOne(targetEntity="Room", inversedBy="rows")
     * @ORM\JoinColumn(name="room_id", nullable=true)
     */
    protected $room;

    /**
     * @param \Application\Entity\Room $room
     */
    public function setRoom($room)
    {
        $this->room = $room;
    }

    /**
     * @return \Application\Entity\Room
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * @param int $time
     */
    public function setTime($time)
    {
        $this->time = $time;
    }

    /**
     * @return int
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param mixed $typeId
     */
    public function setTypeId($typeId)
    {
        $this->typeId = $typeId;
    }

    /**
     * @return mixed
     */
    public function getTypeId()
    {
        return $this->typeId;
    }

}
