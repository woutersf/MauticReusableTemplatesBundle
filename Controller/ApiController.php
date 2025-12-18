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
        // Log that we reached this method
        error_log('Reusable Templates: getTemplatesAction called');

        /** @var TemplateModel $model */
        $model = $this->getModel('reusabletemplate.template');

        if (!$model) {
            error_log('Reusable Templates: Model not found');
            return new JsonResponse([
                'success' => false,
                'error' => 'Model not found',
                'templates' => [],
            ]);
        }

        try {
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
                    'type' => $template->getType() ?? 'section',
                ];
            }

            error_log('Reusable Templates: Returning ' . count($data) . ' templates');

            return new JsonResponse([
                'success' => true,
                'templates' => $data,
            ]);
        } catch (\Exception $e) {
            error_log('Reusable Templates: Error - ' . $e->getMessage());
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage(),
                'templates' => [],
            ]);
        }
    }
}
