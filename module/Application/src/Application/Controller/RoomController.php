<?php

namespace Application\Controller;

use Doctrine\Tests\ORM\Functional\Ticket\Entity;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class RoomController extends AbstractActionController
{
    public function indexAction()
    {
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
        $objectManager = $this
            ->getServiceLocator()
            ->get('Doctrine\ORM\EntityManager');

        $roomId = $this->getEvent()->getRouteMatch()->getParam('id');
        $room = $objectManager->find('Application\Entity\Room', $roomId);

        return new ViewModel([
            'room' => $room,
        ]);
    }

    public function messageAction()
    {
        if ($this->zfcUserAuthentication()->hasIdentity()) {
            $roomId = $this->getEvent()->getRouteMatch()->getParam('id');
            $messageText = $this->getEvent()->getRouteMatch()->getParam('message');

            if ($roomId && $messageText) {
                $objectManager = $this
                    ->getServiceLocator()
                    ->get('Doctrine\ORM\EntityManager');

                // Get Room

                $room = $objectManager->find('Application\Entity\Room', $roomId);

                // Get Type

                $type = $objectManager->find('Application\Entity\RowType', 1); // FIXME: Using hardcoded ID!

                // Set Row

                $row = new \Application\Entity\Row();
                $row->setType($type);
                $row->setTime(new \DateTime('now'));
                $row->setRoom($room);
                $row->setUser($this->zfcUserAuthentication()->getIdentity());

                $objectManager->persist($row);
                $objectManager->flush();

                // Set Message

                $message = new \Application\Entity\Message();
                $message->setValue($messageText);
                $message->setRow($row);

                $objectManager->persist($message);
                $objectManager->flush();

                return new ViewModel([
                    'message' => $message,
                    'row'     => $row,
                ]);
            }
        }

        return new ViewModel();
    }

    public function rollAction()
    {
        if ($this->zfcUserAuthentication()->hasIdentity()) {
            $roomId = $this->getEvent()->getRouteMatch()->getParam('id');
            $sides = intval($this->getEvent()->getRouteMatch()->getParam('sides'));

            if ($roomId && $sides && $sides > 0) {
                $objectManager = $this
                    ->getServiceLocator()
                    ->get('Doctrine\ORM\EntityManager');

                // Get Room

                $room = $objectManager->find('Application\Entity\Room', $roomId);

                // Get Type

                $type = $objectManager->find('Application\Entity\RowType', 2); // FIXME: Using hardcoded ID!

                // Set Row

                $row = new \Application\Entity\Row();
                $row->setType($type);
                $row->setTime(new \DateTime('now'));
                $row->setRoom($room);
                $row->setUser($this->zfcUserAuthentication()->getIdentity());

                $objectManager->persist($row);
                $objectManager->flush();

                // Set Roll Result

                $result = mt_rand(1, $sides);

                $roll = new \Application\Entity\Roll();
                $roll->setValue($result);
                $roll->setSides($sides);
                $roll->setRow($row);

                $objectManager->persist($roll);
                $objectManager->flush();

                return new ViewModel([
                    'roll' => $roll,
                    'row'  => $row,
                ]);
            }
        }

        return new ViewModel();
    }
}
