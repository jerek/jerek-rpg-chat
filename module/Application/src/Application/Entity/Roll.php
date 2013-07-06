<?php

namespace Application\Entity;
use Doctrine\ORM\Mapping as ORM;
use JerekBase\Entity\IdPropertyTrait;

/**
 * @ORM\Entity
 * @ORM\Table(name="roll")
 */
class Roll {
    use IdPropertyTrait;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $value;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $sides;

    /**
     * @var Row
     * @ORM\ManyToOne(targetEntity="Row", inversedBy="rolls")
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
     * @param int $sides
     */
    public function setSides($sides)
    {
        $this->sides = $sides;
    }

    /**
     * @return int
     */
    public function getSides()
    {
        return $this->sides;
    }

    /**
     * @param int $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }
}
