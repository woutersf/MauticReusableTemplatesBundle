<?php

declare(strict_types=1);

namespace MauticPlugin\MauticReusableTemplatesBundle\Controller;

use Mautic\CoreBundle\Controller\FormController;
use Mautic\CoreBundle\Factory\PageHelperFactoryInterface;
use MauticPlugin\MauticReusableTemplatesBundle\Entity\ReusableTemplate;
use MauticPlugin\MauticReusableTemplatesBundle\Model\TemplateModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TemplateController extends FormController
{
    public function indexAction(Request $request, PageHelperFactoryInterface $pageHelperFactory, int $page = 1): Response
    {
        $this->setListFilters();

        $pageHelper = $pageHelperFactory->make('mautic.reusabletemplate.template', $page);

        $limit      = $pageHelper->getLimit();
        $start      = $pageHelper->getStart();
        $orderBy    = $request->getSession()->get('mautic.reusabletemplate.template.orderby', 't.name');
        $orderByDir = $request->getSession()->get('mautic.reusabletemplate.template.orderbydir', 'ASC');
        $filter     = $request->get('search', $request->getSession()->get('mautic.reusabletemplate.template.filter', ''));
        $tmpl       = $request->isXmlHttpRequest() ? $request->get('tmpl', 'index') : 'index';

        /** @var TemplateModel $model */
        $model = $this->getModel('reusabletemplate.template');
        $items = $model->getEntities([
            'start'      => $start,
            'limit'      => $limit,
            'filter'     => $filter,
            'orderBy'    => $orderBy,
            'orderByDir' => $orderByDir,
        ]);

        $request->getSession()->set('mautic.reusabletemplate.template.filter', $filter);

        $count = count($items);
        if ($count && $count < ($start + 1)) {
            $lastPage  = $pageHelper->countPage($count);
            $returnUrl = $this->generateUrl('mautic_reusabletemplate_template_index', ['page' => $lastPage]);
            $pageHelper->rememberPage($lastPage);

            return $this->postActionRedirect([
                'returnUrl'      => $returnUrl,
                'viewParameters' => [
                    'page' => $lastPage,
                    'tmpl' => $tmpl,
                ],
                'contentTemplate' => 'MauticPlugin\MauticReusableTemplatesBundle\Controller\TemplateController::indexAction',
                'passthroughVars' => [
                    'activeLink'    => '#mautic_reusabletemplate_template_index',
                    'mauticContent' => 'reusabletemplate_template',
                ],
            ]);
        }

        $pageHelper->rememberPage($page);

        return $this->delegateView([
            'viewParameters'  => [
                'items'       => $items,
                'searchValue' => $filter,
                'page'        => $page,
                'limit'       => $limit,
                'tmpl'        => $tmpl,
            ],
            'contentTemplate' => '@MauticReusableTemplates/Template/list.html.twig',
            'passthroughVars' => [
                'route'         => $this->generateUrl('mautic_reusabletemplate_template_index', ['page' => $page]),
                'mauticContent' => 'reusabletemplate_template',
            ],
        ]);
    }

    public function newAction(Request $request): Response
    {
        $entity = new ReusableTemplate();

        // Set default type and content for new templates
        $entity->setType('section');
        $defaultContent = '<mj-column css-class="content-padding" data-reusablesectionId="{id}">
        <mj-text font-size="20px" font-weight="600" color="#1A1A1A">
                      REUSABLE SECTION
        </mj-text>
</mj-column>';
        $entity->setContent($defaultContent);

        /** @var TemplateModel $model */
        $model = $this->getModel('reusabletemplate.template');

        $returnUrl = $this->generateUrl('mautic_reusabletemplate_template_index');
        $page      = $request->getSession()->get('mautic.reusabletemplate.template.page', 1);
        $action    = $this->generateUrl('mautic_reusabletemplate_template_action', ['objectAction' => 'new']);

        $form = $model->createForm($entity, $this->formFactory, $action);

        if ('POST' === $request->getMethod()) {
            $valid = false;
            if (!$cancelled = $this->isFormCancelled($form)) {
                if ($valid = $this->isFormValid($form)) {
                    $model->saveEntity($entity);

                    $this->addFlashMessage('mautic.core.notice.created', [
                        '%name%'      => $entity->getName(),
                        '%menu_link%' => 'mautic_reusabletemplate_template_index',
                        '%url%'       => $this->generateUrl('mautic_reusabletemplate_template_action', [
                            'objectAction' => 'edit',
                            'objectId'     => $entity->getId(),
                        ]),
                    ]);
                }
            }

            if ($cancelled || ($valid && $this->getFormButton($form, ['buttons', 'save'])->isClicked())) {
                return $this->postActionRedirect([
                    'returnUrl'       => $returnUrl,
                    'viewParameters'  => ['page' => $page],
                    'contentTemplate' => 'MauticPlugin\MauticReusableTemplatesBundle\Controller\TemplateController::indexAction',
                    'passthroughVars' => [
                        'activeLink'    => '#mautic_reusabletemplate_template_index',
                        'mauticContent' => 'reusabletemplate_template',
                    ],
                ]);
            } elseif ($valid) {
                return $this->editAction($request, $entity->getId(), true);
            }
        }

        return $this->delegateView([
            'viewParameters' => [
                'form' => $form->createView(),
            ],
            'contentTemplate' => '@MauticReusableTemplates/Template/form.html.twig',
            'passthroughVars' => [
                'activeLink'    => '#mautic_reusabletemplate_template_new',
                'route'         => $action,
                'mauticContent' => 'reusabletemplate_template',
            ],
        ]);
    }

    public function editAction(Request $request, int $objectId, bool $ignorePost = false): Response
    {
        /** @var TemplateModel $model */
        $model  = $this->getModel('reusabletemplate.template');
        $entity = $model->getEntity($objectId);

        $page = $request->getSession()->get('mautic.reusabletemplate.template.page', 1);
        $returnUrl = $this->generateUrl('mautic_reusabletemplate_template_index', ['page' => $page]);

        $postActionVars = [
            'returnUrl'       => $returnUrl,
            'viewParameters'  => ['page' => $page],
            'contentTemplate' => 'MauticPlugin\MauticReusableTemplatesBundle\Controller\TemplateController::indexAction',
            'passthroughVars' => [
                'activeLink'    => '#mautic_reusabletemplate_template_index',
                'mauticContent' => 'reusabletemplate_template',
            ],
        ];

        if (null === $entity) {
            return $this->postActionRedirect(
                array_merge($postActionVars, [
                    'flashes' => [
                        [
                            'type'    => 'error',
                            'msg'     => 'mautic.core.error.notfound',
                            'msgVars' => ['%id%' => $objectId],
                        ],
                    ],
                ])
            );
        } elseif ($model->isLocked($entity)) {
            return $this->isLocked($postActionVars, $entity, 'reusabletemplate.template');
        }

        $action = $this->generateUrl('mautic_reusabletemplate_template_action', ['objectAction' => 'edit', 'objectId' => $objectId]);
        $form   = $model->createForm($entity, $this->formFactory, $action);

        if (!$ignorePost && 'POST' === $request->getMethod()) {
            $valid = false;

            if (!$cancelled = $this->isFormCancelled($form)) {
                if ($valid = $this->isFormValid($form)) {
                    $content = $entity->getContent();
                    $templateId = $entity->getId();

                    // Replace {id} placeholder with actual template ID
                    if ($templateId && !empty($content)) {
                        $content = str_replace('{id}', (string) $templateId, $content);
                        $entity->setContent($content);
                    }

                    // Validate that content contains data-reusablesectionId attribute
                    $expectedAttribute = 'data-reusablesectionId="' . $templateId . '"';

                    if ($templateId && !empty($content) && strpos($content, $expectedAttribute) === false) {
                        $valid = false;
                        $this->addFlashMessage('mautic.reusabletemplate.error.missing_attribute', [
                            '%attribute%' => $expectedAttribute,
                        ], 'error');
                    }

                    if ($valid) {
                        $model->saveEntity($entity, $this->getFormButton($form, ['buttons', 'save'])->isClicked());

                        $this->addFlashMessage('mautic.core.notice.updated', [
                            '%name%'      => $entity->getName(),
                            '%menu_link%' => 'mautic_reusabletemplate_template_index',
                            '%url%'       => $this->generateUrl('mautic_reusabletemplate_template_action', [
                                'objectAction' => 'edit',
                                'objectId'     => $entity->getId(),
                            ]),
                        ]);
                    }
                }
            } else {
                $model->unlockEntity($entity);
            }

            if ($cancelled || ($valid && $this->getFormButton($form, ['buttons', 'save'])->isClicked())) {
                return $this->postActionRedirect($postActionVars);
            }
        } else {
            $model->lockEntity($entity);
        }

        return $this->delegateView([
            'viewParameters' => [
                'form' => $form->createView(),
            ],
            'contentTemplate' => '@MauticReusableTemplates/Template/form.html.twig',
            'passthroughVars' => [
                'activeLink'    => '#mautic_reusabletemplate_template_index',
                'route'         => $action,
                'mauticContent' => 'reusabletemplate_template',
            ],
        ]);
    }

    public function deleteAction(Request $request, int $objectId): Response
    {
        $page      = $request->getSession()->get('mautic.reusabletemplate.template.page', 1);
        $returnUrl = $this->generateUrl('mautic_reusabletemplate_template_index', ['page' => $page]);
        $success   = 0;
        $flashes   = [];

        $postActionVars = [
            'returnUrl'       => $returnUrl,
            'viewParameters'  => ['page' => $page],
            'contentTemplate' => 'MauticPlugin\MauticReusableTemplatesBundle\Controller\TemplateController::indexAction',
            'passthroughVars' => [
                'activeLink'    => '#mautic_reusabletemplate_template_index',
                'success'       => $success,
                'mauticContent' => 'reusabletemplate_template',
            ],
        ];

        if (Request::METHOD_POST === $request->getMethod()) {
            /** @var TemplateModel $model */
            $model  = $this->getModel('reusabletemplate.template');
            $entity = $model->getEntity($objectId);

            if (null === $entity) {
                $flashes[] = [
                    'type'    => 'error',
                    'msg'     => 'mautic.core.error.notfound',
                    'msgVars' => ['%id%' => $objectId],
                ];
            } elseif ($model->isLocked($entity)) {
                return $this->isLocked($postActionVars, $entity, 'reusabletemplate.template');
            } else {
                $model->deleteEntity($entity);
                $name      = $entity->getName();
                $flashes[] = [
                    'type'    => 'notice',
                    'msg'     => 'mautic.core.notice.deleted',
                    'msgVars' => [
                        '%name%' => $name,
                        '%id%'   => $objectId,
                    ],
                ];
            }
        }

        return $this->postActionRedirect(
            array_merge($postActionVars, [
                'flashes' => $flashes,
            ])
        );
    }

    public function applyAction(Request $request, int $objectId): Response
    {
        /** @var TemplateModel $model */
        $model  = $this->getModel('reusabletemplate.template');
        $entity = $model->getEntity($objectId);

        if (null === $entity) {
            return $this->postActionRedirect([
                'returnUrl'       => $this->generateUrl('mautic_reusabletemplate_template_index'),
                'viewParameters'  => ['page' => 1],
                'contentTemplate' => 'MauticPlugin\MauticReusableTemplatesBundle\Controller\TemplateController::indexAction',
                'flashes' => [
                    [
                        'type'    => 'error',
                        'msg'     => 'mautic.core.error.notfound',
                        'msgVars' => ['%id%' => $objectId],
                    ],
                ],
            ]);
        }

        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();

        $searchPattern = 'data-reusablesectionId="' . $objectId . '"';

        $sql = "SELECT id, name, email_type, subject
                FROM emails
                WHERE custom_html LIKE :pattern OR plain_text LIKE :pattern";

        $stmt = $connection->prepare($sql);
        $stmt->bindValue('pattern', '%' . $searchPattern . '%');
        $result = $stmt->executeQuery();
        $emails = $result->fetchAllAssociative();

        return $this->delegateView([
            'viewParameters'  => [
                'entity'   => $entity,
                'emails'   => $emails,
                'objectId' => $objectId,
            ],
            'contentTemplate' => '@MauticReusableTemplates/Template/apply.html.twig',
            'passthroughVars' => [
                'activeLink'    => '#mautic_reusabletemplate_template_index',
                'route'         => $this->generateUrl('mautic_reusabletemplate_template_action', ['objectAction' => 'apply', 'objectId' => $objectId]),
                'mauticContent' => 'reusabletemplate_template',
            ],
        ]);
    }

    public function applyProcessAction(Request $request, int $objectId): Response
    {
        if (Request::METHOD_POST !== $request->getMethod()) {
            return $this->accessDenied();
        }

        /** @var TemplateModel $model */
        $model  = $this->getModel('reusabletemplate.template');
        $entity = $model->getEntity($objectId);

        if (null === $entity) {
            return $this->postActionRedirect([
                'returnUrl'       => $this->generateUrl('mautic_reusabletemplate_template_index'),
                'viewParameters'  => ['page' => 1],
                'contentTemplate' => 'MauticPlugin\MauticReusableTemplatesBundle\Controller\TemplateController::indexAction',
                'flashes' => [
                    [
                        'type'    => 'error',
                        'msg'     => 'mautic.core.error.notfound',
                        'msgVars' => ['%id%' => $objectId],
                    ],
                ],
            ]);
        }

        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();

        $searchPattern = 'data-reusablesectionId="' . $objectId . '"';

        $sql = "SELECT id, custom_html, plain_text
                FROM emails
                WHERE custom_html LIKE :pattern OR plain_text LIKE :pattern";

        $stmt = $connection->prepare($sql);
        $stmt->bindValue('pattern', '%' . $searchPattern . '%');
        $result = $stmt->executeQuery();
        $emails = $result->fetchAllAssociative();

        $replacementCount = 0;

        foreach ($emails as $email) {
            $replacementCount++;
        }

        return $this->delegateView([
            'viewParameters'  => [
                'entity'           => $entity,
                'replacementCount' => $replacementCount,
                'objectId'         => $objectId,
            ],
            'contentTemplate' => '@MauticReusableTemplates/Template/apply_result.html.twig',
            'passthroughVars' => [
                'activeLink'    => '#mautic_reusabletemplate_template_index',
                'mauticContent' => 'reusabletemplate_template',
            ],
        ]);
    }

    public function executeAction(Request $request, $objectAction, $objectId = 0, $objectSubId = 0, $objectModel = ''): Response
    {
        return match ($objectAction) {
            'new' => $this->newAction($request),
            'edit' => $this->editAction($request, (int) $objectId),
            'delete' => $this->deleteAction($request, (int) $objectId),
            'apply' => $this->applyAction($request, (int) $objectId),
            'applyProcess' => $this->applyProcessAction($request, (int) $objectId),
            default => $this->accessDenied(),
        };
    }
}
