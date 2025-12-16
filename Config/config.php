<?php

declare(strict_types=1);

return [
    'name'        => 'Reusable Email Parts',
    'description' => 'Manage reusable HTML templates for Mautic emails',
    'version'     => '1.0.0',
    'author'      => 'Frederik Wouters',
    'icon'        => 'plugins/MauticReusableTemplatesBundle/Assets/reusable.png',

    'routes' => [
        'public' => [
            'mautic_reusabletemplate_api_templates' => [
                'path'       => '/reusabletemplates/list',
                'controller' => 'MauticPlugin\MauticReusableTemplatesBundle\Controller\ApiController::getTemplatesAction',
            ],
        ],
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
        'admin' => [
            'mautic.reusabletemplate.menu.root' => [
                'id'        => 'mautic_reusabletemplate_root',
                'iconClass' => 'ri-file-copy-line',
                'priority'  => 1,
                'checks'    => [
                    'integration' => [
                        'ReusableTemplates' => [
                            'enabled' => true,
                        ],
                    ],
                ],
            ],
            'mautic.reusabletemplate.menu.emailparts' => [
                'route'     => 'mautic_reusabletemplate_template_index',
                'parent'    => 'mautic.reusabletemplate.menu.root',
                'iconClass' => 'ri-file-copy-line',
                'priority'  => 1,
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
        'events' => [
            'mautic.reusabletemplate.asset.subscriber' => [
                'class' => MauticPlugin\MauticReusableTemplatesBundle\EventListener\AssetSubscriber::class,
                'tags' => [
                    'kernel.event_subscriber',
                ],
            ],
        ],
        'commands' => [
            'mautic.reusabletemplate.command.process_changed' => [
                'class' => MauticPlugin\MauticReusableTemplatesBundle\Command\ProcessChangedTemplatesCommand::class,
                'arguments' => [
                    'doctrine.orm.entity_manager',
                ],
                'tags' => [
                    'console.command',
                ],
            ],
        ],
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
