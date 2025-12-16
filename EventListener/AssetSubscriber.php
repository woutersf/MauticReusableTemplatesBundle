<?php

declare(strict_types=1);

namespace MauticPlugin\MauticReusableTemplatesBundle\EventListener;

use Mautic\CoreBundle\CoreEvents;
use Mautic\CoreBundle\Event\CustomAssetsEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AssetSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            CoreEvents::VIEW_INJECT_CUSTOM_ASSETS => ['injectAssets', 0],
        ];
    }

    public function injectAssets(CustomAssetsEvent $event): void
    {
        // Load JavaScript for GrapesJS integration
        $event->addScript('plugins/MauticReusableTemplatesBundle/Assets/js/grapesjs-reusabletemplates.js');
    }
}
