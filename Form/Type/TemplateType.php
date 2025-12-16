<?php

declare(strict_types=1);

namespace MauticPlugin\MauticReusableTemplatesBundle\Form\Type;

use Mautic\CoreBundle\Form\Type\FormButtonsType;
use Symfony\Component\Form\AbstractType;
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

        $builder->add('content', TextareaType::class, [
            'label' => 'HTML Content',
            'label_attr' => ['class' => 'control-label'],
            'attr' => [
                'class' => 'form-control',
                'rows' => 20,
                'placeholder' => 'Enter your reusable template HTML content',
            ],
            'required' => false,
            'help' => 'Enter your reusable HTML template content. This can be used across different emails and campaigns.',
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
