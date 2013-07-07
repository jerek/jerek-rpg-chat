<?php

namespace Application\Entity;
use Doctrine\ORM\Mapping as ORM;
use JerekBase\Entity\IdPropertyTrait;
use JerekUser\Entity\UserPropertyTrait;

/**
 * @ORM\Entity(repositoryClass="Application\Repository\Row")
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
     * @var RowType
     * @ORM\ManyToOne(targetEntity="RowType", inversedBy="rows")
     * @ORM\JoinColumn(name="type_id")
     */
    protected $type;

    /**
     * @var Room
     * @ORM\ManyToOne(targetEntity="Room", inversedBy="rows")
     * @ORM\JoinColumn(name="room_id", nullable=true)
     */
    protected $room;

    /**
     * @var Roll
     * @ORM\OneToMany(targetEntity="Roll", mappedBy="row")
     */
    protected $rolls;

    /**
     * @var Message
     * @ORM\OneToOne(targetEntity="Message", mappedBy="row")
     */
    protected $message;

    // Getters and Setters

    /**
     * @param \Application\Entity\Message $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return \Application\Entity\Message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param \Application\Entity\Roll $rolls
     */
    public function setRolls($rolls)
    {
        $this->rolls = $rolls;
    }

    /**
     * @return \Application\Entity\Roll
     */
    public function getRolls()
    {
        return $this->rolls;
    }

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
     * @param \Application\Entity\RowType $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return \Application\Entity\RowType
     */
    public function getType()
    {
        return $this->type;
    }
}
