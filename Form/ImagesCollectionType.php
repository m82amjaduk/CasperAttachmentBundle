<?php

namespace Casper\AttachmentBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Image attachments collection form type
 *
 * @package Casper\AttachmentBundle
 */
class ImagesCollectionType extends CollectionType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var ImageAttachmentType $type */
        $type            = $options['type'];
        $type->dataClass = $options['item_data_class'];
        parent::buildForm($builder, $options);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(
            array(
                'type'            => new ImageAttachmentType(),
                'allow_add'       => true,
                'allow_delete'    => true,
                'by_reference'    => false,
                'prototype'       => true,
                'item_data_class' => 'Casper\AttachmentBundle\Entity\AbstractMultiAttachment'
            )
        );
    }

    public function getName()
    {
        return 'images_collection';
    }
}
