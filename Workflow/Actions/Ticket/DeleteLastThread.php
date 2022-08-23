<?php

namespace Webkul\UVDesk\CoreFrameworkBundle\Workflow\Actions\Ticket;

use Webkul\UVDesk\AutomationBundle\PreparedResponse\FunctionalGroup;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Webkul\UVDesk\CoreFrameworkBundle\Entity\Ticket;
use Webkul\UVDesk\AutomationBundle\Workflow\Action as WorkflowAction;
use Webkul\UVDesk\CoreFrameworkBundle\Entity\Thread;

class DeleteLastThread extends WorkflowAction
{
    public static function getId()
    {
        return 'uvdesk.ticket.delete_last_thread';
    }

    public static function getDescription()
    {
        return "Delete last thread";
    }

    public static function getFunctionalGroup()
    {
        return FunctionalGroup::TICKET;
    }

    public static function getOptions(ContainerInterface $container)
    {
        return null;
    }

    public static function applyAction(ContainerInterface $container, $entity, $value = null)
    {
        $entityManager = $container->get('doctrine.orm.entity_manager');
        if($entity instanceof Ticket) {            
            $newest = $entityManager->createQueryBuilder()
                                    ->select("th")
                                    ->from(Thread::class, "th")
                                    ->where("th.ticket = :ticket")
                                    ->setParameter("ticket", $entity)
                                    ->orderBy("th.id", "DESC")
                                    ->setMaxResults(1)
                                    ->getQuery()
                                    ->getOneOrNullResult();
            $entityManager->remove($newest);
            $entityManager->flush();
        }
    }
}