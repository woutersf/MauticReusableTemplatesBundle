<?php

declare(strict_types=1);

return [
    'name'        => 'Reusable Email Parts',
    'description' => 'Manage reusable HTML templates for Mautic emails',
    'version'     => '1.0.0',
    'author'      => 'Frederik Wouters',
    'icon'        => 'plugins/MauticReusableTemplatesBundle/Assets/reusable.png',

    'routes' => [
        'main' => [
            'mautic_reusabletemplate_template_index' => [
                'path'       => '/reusabletemplates/{page}',
                'controller' => 'MauticPlugin\MauticReusableTemplatesBundle\Controller\TemplateController::indexAction',
                'defaults'   => [
                    'page' => 1,
                ],
            ],
            'mautic_reusabletemplate_template_action' => [
                'path'       => '/reusabletemplates/{objectAction}/{objectId}',
                'controller' => 'MauticPlugin\MauticReusableTemplatesBundle\Controller\TemplateController::executeAction',
                'defaults'   => [
                    'objectId' => 0,
                ],
            ],
        ],
    ],

    'menu' => [
        'main' => [
            'mautic.reusabletemplate.menu.index' => [
                'route'     => 'mautic_reusabletemplate_template_index',
                'iconClass' => 'ri-file-copy-line',
                'parent'    => 'mautic.core.channels',
                'priority'  => 85,
                'checks'    => [
                    'integration' => [
                        'ReusableTemplates' => [
                            'enabled' => true,
                        ],
                    ],
                ],
            ],
        ],
    ],

    'services' => [
        'integrations' => [
            'mautic.integration.reusabletemplates' => [
                'class' => MauticPlugin\MauticReusableTemplatesBundle\Integration\ReusableTemplatesIntegration::class,
                'tags'  => [
                    'mautic.integration',
                    'mautic.basic_integration',
                ],
            ],
            'mautic.integration.reusabletemplates.configuration' => [
                'class' => MauticPlugin\MauticReusableTemplatesBundle\Integration\Support\ConfigSupport::class,
                'tags'  => [
                    'mautic.config_integration',
                ],
            ],
        ],
        'models' => [
            'mautic.reusabletemplate.model.template' => [
                'class'     => MauticPlugin\MauticReusableTemplatesBundle\Model\TemplateModel::class,
                'arguments' => [
                    'doctrine.orm.entity_manager',
                    'mautic.security',
                    'event_dispatcher',
                    'router',
                    'translator',
                    'mautic.helper.user',
                    'monolog.logger.mautic',
                    'mautic.helper.core_parameters',
                ],
            ],
        ],
        'forms' => [
            'mautic.reusabletemplate.form.type.template' => [
                'class' => MauticPlugin\MauticReusableTemplatesBundle\Form\Type\TemplateType::class,
                'tags'  => [
                    'form.type',
                ],
            ],
        ],
    ],

    'parameters' => [
        'reusabletemplate_enabled' => true,
    ],
];
