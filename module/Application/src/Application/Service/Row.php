<?php

namespace Application\Service;

use Zend\Json\Json;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Class Row
 * @package Application\Service
 */
class Row implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Gets entire row history.
     * @return string
     */
    public function getRowData($rowId)
    {
        /** @var \Application\Repository\Row $rowRepository */
        $rowRepository = $this
            ->getServiceLocator()
            ->get('Doctrine\ORM\EntityManager')
            ->getRepository('Application\Entity\Row');
        return $rowRepository->getRowData($rowId);
    }
}
