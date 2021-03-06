<?php

namespace Application\Controller;

use Application\Entity\Message;
use Application\Entity\Roll;
use Application\Entity\Row;
use Doctrine\ORM\Tools\Export\ExportException;
use Doctrine\Tests\ORM\Functional\Ticket\Entity;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class RoomController extends AbstractActionController
{
    public function indexAction()
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            throw new \Exception('You must sign in to see what rooms you have access to.');
        }

        $roomRepository = $this
            ->getServiceLocator()
            ->get('Doctrine\ORM\EntityManager')
            ->getRepository('Application\Entity\Room');

        return new ViewModel([
            'rooms' => $roomRepository->findAll(),
        ]);
    }

    public function viewAction()
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            throw new \Exception('You must sign in to access this room.');
        }

        $roomId = $this->getEvent()->getRouteMatch()->getParam('id');
        $roomService = $this->getServiceLocator()->get('Application\Service\Room');

        return new ViewModel([
            'room'  => $roomService->getRoomData($roomId),
        ]);
    }

    public function contentAction()
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            throw new \Exception('You must sign in to interact with this room.');
        }

        $roomId = $this->getEvent()->getRouteMatch()->getParam('id');
        $typeName = $this->getEvent()->getRouteMatch()->getParam('type');
        $value = $this->getEvent()->getRouteMatch()->getParam('value');

        // FIXME: Verify user has permissions in room

        $objectManager = $this
            ->getServiceLocator()
            ->get('Doctrine\ORM\EntityManager');

        // Get Room

        $room = $objectManager->find('Application\Entity\Room', $roomId);

        if (!$room) {
            throw new \Exception('Room not found.');
        }

        // Get Type

        $type = $objectManager
            ->getRepository('Application\Entity\RowType')
            ->findOneBy(array('name' => $typeName));

        if (!$type) {
            throw new \Exception('Content type not found.');
        }

        // Set Row

        $row = new Row();
        $row->setType($type);
        $row->setTime(new \DateTime('now'));
        $row->setRoom($room);
        $row->setUser($this->zfcUserAuthentication()->getIdentity());

        $objectManager->persist($row);

        if ($type->getName() == 'message') {
            // Set message

            $content = new Message();
            $content->setValue($value);

            $content->setRow($row);
            $objectManager->persist($content);
        } elseif ($type->getName() == 'roll') {
            // Calculate and set roll result

            if (preg_match('/^[0-9]+$/', $value)) {
                // Single die

                $result = mt_rand(1, $value);

                $content = new Roll();
                $content->setValue($result);
                $content->setSides($value);

                $content->setRow($row);
                $objectManager->persist($content);
            } elseif (preg_match('/^([0-9]+)d([0-9]+)$/', $value, $dice)) {
                // Multiple dice

                $count = $dice[1];
                $sides = $dice[2];

                for ($i = 0; $i < $count; $i++) {
                    $result = mt_rand(1, $sides);

                    $content = new Roll();
                    $content->setValue($result);
                    $content->setSides($sides);

                    $content->setRow($row);
                    $objectManager->persist($content);
                }
            } else {
                throw new \Exception('Poorly formed roll request.');
            }
        } else {
            throw new \Exception('Unhandled content type.');
        }

        $objectManager->flush();

        $rowId = $row->getId();
        $rowService = $this->getServiceLocator()->get('Application\Service\Row');

        $viewModel = new ViewModel([
            'row' => $rowService->getRowData($rowId)
        ]);

        return $viewModel;
    }
}
