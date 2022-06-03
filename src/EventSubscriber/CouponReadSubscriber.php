<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Coupons;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class CouponReadSubscriber implements EventSubscriberInterface
{
    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => [
                ['hasFilter', EventPriorities::PRE_READ],
            ],
        ];
    }

    public function hasFilter(ControllerEvent $event)
    {
        // first check if this affects the requested resource
        $resource = $event->getRequest()->attributes->get('_api_resource_class');

        if (Coupons::class !== $resource) {
            return;
        }

        // second check if this is the get_collection controller
        $controller = $event->getRequest()->attributes->get('_controller');

        if ('api_platform.action.get_collection' !== $controller) {
            return;
        }

        // third validate the required filter is set
        // we expect a filter via GET parameter 'filter-query-parameter'
        if (!$event->getRequest()->query->has('coupon') || !$event->getRequest()->query->has('valid') || !$event->getRequest()->query->has('expireDate')) {
            throw new BadRequestHttpException('Filters are required');
        }

        if($event->getRequest()->query->get('valid') !== "1"){
            throw new BadRequestHttpException('The coupon has to be valid');
        }

        if(key($event->getRequest()->query->get('expireDate')) !== "after"){
            throw new BadRequestHttpException('The expire date should be after the current date');
        }
    }
}