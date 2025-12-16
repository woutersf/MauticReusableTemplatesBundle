<?php

declare(strict_types=1);

namespace MauticPlugin\MauticReusableTemplatesBundle\Model;

use Mautic\CoreBundle\Model\FormModel;
use MauticPlugin\MauticReusableTemplatesBundle\Entity\ReusableTemplate;
use MauticPlugin\MauticReusableTemplatesBundle\Entity\ReusableTemplateRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class TemplateModel extends FormModel
{
    public function getRepository(): ReusableTemplateRepository
    {
        return $this->em->getRepository(ReusableTemplate::class);
    }

    public function getPermissionBase(): string
    {
        return 'plugin:reusabletemplates:templates';
    }

    public function getEntity($id = null): ?ReusableTemplate
    {
        if (null === $id) {
            return new ReusableTemplate();
        }

        return parent::getEntity($id);
    }

    protected function dispatchEvent($action, &$entity, $isNew = false, ?\Symfony\Contracts\EventDispatcher\Event $event = null): ?\Symfony\Contracts\EventDispatcher\Event
    {
        // No events for now
        return null;
    }

    public function saveEntity($entity, $unlock = true): void
    {
        if (!$entity instanceof ReusableTemplate) {
            throw new MethodNotAllowedHttpException(['ReusableTemplate']);
        }

        $isNew = $entity->getId() === null;

        if ($isNew) {
            $entity->setCreatedAt(new \DateTime());
        } else {
            $entity->setUpdatedAt(new \DateTime());
            $currentUser = $this->userHelper->getUser();
            if ($currentUser) {
                $entity->setUpdatedBy($currentUser->getId());
            }
        }

        // Set changed flag to trigger email content update
        $entity->setChanged(true);

        parent::saveEntity($entity, $unlock);
    }

    public function createForm($entity, FormFactoryInterface $formFactory, $action = null, $options = []): \Symfony\Component\Form\FormInterface
    {
        if (!$entity instanceof ReusableTemplate) {
            throw new MethodNotAllowedHttpException(['ReusableTemplate']);
        }

        if (!empty($action)) {
            $options['action'] = $action;
        }

        return $formFactory->create(\MauticPlugin\MauticReusableTemplatesBundle\Form\Type\TemplateType::class, $entity, $options);
    }
}
