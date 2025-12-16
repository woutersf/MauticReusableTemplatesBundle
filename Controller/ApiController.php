<?php

declare(strict_types=1);

namespace MauticPlugin\MauticReusableTemplatesBundle\Controller;

use Mautic\CoreBundle\Controller\CommonController;
use MauticPlugin\MauticReusableTemplatesBundle\Model\TemplateModel;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends CommonController
{
    public function getTemplatesAction(Request $request): JsonResponse
    {
        /** @var TemplateModel $model */
        $model = $this->getModel('reusabletemplate.template');

        // Get all templates
        $templates = $model->getEntities([
            'orderBy' => 't.name',
            'orderByDir' => 'ASC',
        ]);

        $data = [];
        foreach ($templates as $template) {
            $data[] = [
                'id' => $template->getId(),
                'name' => $template->getName(),
                'content' => $template->getContent(),
            ];
        }

        return new JsonResponse([
            'success' => true,
            'templates' => $data,
        ]);
    }
}
