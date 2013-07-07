<?php

namespace Application\Service;

use Zend\Json\Json;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Class Room
 * @package Application\Service
 */
class Room implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Gets entire room history.
     * @return string
     */
    public function getRoomData($roomId)
    {
        /** @var \Application\Repository\Room $roomRepository */
        $roomRepository = $this
            ->getServiceLocator()
            ->get('Doctrine\ORM\EntityManager')
            ->getRepository('Application\Entity\Room');
        return $roomRepository->getRoomData($roomId);
    }
}
