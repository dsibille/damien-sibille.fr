<?php

namespace Sibille\EcommerceBundle\Form;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContactType extends AbstractType
{
    
    public function __construct() {
                
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', 'text', array('required' => true, 'error_bubbling' => true) )
            ->add('prenom', 'text', array('required' => true, 'error_bubbling' => true) )
            ->add('email', 'email', array('required' => true, 'error_bubbling' => true) )
            ->add('telephone', 'text', array('required' => false, 'error_bubbling' => true) )
            //->add('sujet', 'text', array('required' => true, 'error_bubbling' => true) )
            ->add('commentaire', 'textarea', array('required' => true, 'error_bubbling' => true) )
            ;

        }
    
    /**
     * @return string
     */
    public function getName()
    {
        return 'contact';
    }
}
