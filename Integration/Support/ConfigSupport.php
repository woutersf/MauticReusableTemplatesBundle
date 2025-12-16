<?php

declare(strict_types=1);

namespace MauticPlugin\MauticReusableTemplatesBundle\Integration\Support;

use Mautic\IntegrationsBundle\Integration\DefaultConfigFormTrait;
use Mautic\IntegrationsBundle\Integration\Interfaces\ConfigFormInterface;
use MauticPlugin\MauticReusableTemplatesBundle\Integration\ReusableTemplatesIntegration;

class ConfigSupport extends ReusableTemplatesIntegration implements ConfigFormInterface
{
    use DefaultConfigFormTrait;
}
