<?php

namespace Application\Entity;
use Doctrine\ORM\Mapping as ORM;
use JerekBase\Entity\IdPropertyTrait;

/**
 * @ORM\Entity
 * @ORM\Table(name="message")
 */
class Message {
    use IdPropertyTrait;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $value;

    /**
     * @var Row
     * @ORM\OneToOne(targetEntity="Row", inversedBy="message")
     * @ORM\JoinColumn(name="row_id")
     */
    protected $row;

    // Getters and Setters

    /**
     * @param \Application\Entity\Row $row
     */
    public function setRow($row)
    {
        $this->row = $row;
    }

    /**
     * @return \Application\Entity\Row
     */
    public function getRow()
    {
        return $this->row;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
