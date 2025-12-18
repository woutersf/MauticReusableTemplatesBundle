<?php

declare(strict_types=1);

namespace MauticPlugin\MauticReusableTemplatesBundle\Form\Type;

use Mautic\CoreBundle\Form\Type\FormButtonsType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use MauticPlugin\MauticReusableTemplatesBundle\Entity\ReusableTemplate;

class TemplateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', TextType::class, [
            'label' => 'Template Name',
            'label_attr' => ['class' => 'control-label required'],
            'attr' => [
                'class' => 'form-control',
                'placeholder' => 'Enter template name',
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Template name is required',
                ]),
            ],
        ]);

        $builder->add('type', ChoiceType::class, [
            'label' => 'Component Type',
            'label_attr' => ['class' => 'control-label required'],
            'attr' => [
                'class' => 'form-control',
            ],
            'choices' => [
                'Section (mj-section)' => 'section',
                'Column (mj-column)' => 'column',
                'Text (mj-text)' => 'text',
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Component type is required',
                ]),
            ],
            'help' => 'Select the MJML component type that matches your template structure.',
        ]);

        $builder->add('content', TextareaType::class, [
            'label' => 'HTML Content',
            'label_attr' => ['class' => 'control-label'],
            'attr' => [
                'class' => 'form-control',
                'rows' => 20,
            ],
            'required' => false,
            'help' => 'Enter your reusable HTML template content. The outer MJML component must contain the data attribute: data-reusablesectionId="{id}" for tracking purposes. The {id} placeholder will be automatically replaced with the template ID on save.',
        ]);

        $builder->add('buttons', FormButtonsType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ReusableTemplate::class,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'reusabletemplate';
    }
}
