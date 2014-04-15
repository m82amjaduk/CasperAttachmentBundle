<?php

namespace Casper\AttachmentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Image attachment form type
 *
 * @package Casper\AttachmentBundle
 */
class ImageAttachmentType extends AbstractType
{
    public $dataClass = 'Casper\AttachmentBundle\Entity\AbstractMultiAttachment';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('uploadedFile', 'file', array('data_class' => null))
            ->add('isPrimary',    'checkbox', array('label' => 'Is primary'));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class' => $this->dataClass));
    }

    public function getName()
    {
        return 'image_attachment';
    }
}
