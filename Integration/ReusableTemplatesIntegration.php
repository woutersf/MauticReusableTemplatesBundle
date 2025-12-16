<?php

declare(strict_types=1);

namespace MauticPlugin\MauticReusableTemplatesBundle\Integration;

use Mautic\IntegrationsBundle\Integration\BasicIntegration;
use Mautic\IntegrationsBundle\Integration\ConfigurationTrait;
use Mautic\IntegrationsBundle\Integration\Interfaces\BasicInterface;

class ReusableTemplatesIntegration extends BasicIntegration implements BasicInterface
{
    use ConfigurationTrait;

    public const NAME = 'ReusableTemplates';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getDisplayName(): string
    {
        return 'Reusable Email Parts';
    }

    public function getIcon(): string
    {
        return 'plugins/MauticReusableTemplatesBundle/Assets/reusable.png';
    }
}
