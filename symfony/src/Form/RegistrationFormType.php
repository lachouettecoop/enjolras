<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('civilite',  ChoiceType::class,
                  array('choices' => array(
                      'Monsieur' => 'mr',
                      'Madame' => 'mme',
                      'Mademoiselle' => 'mlle'),
                        'label' => 'Civilité',
                        'attr' => array('class' => 'form-control')
                       )
                 )
            ->add('nom', null, array('label' => 'Nom', 'attr' => array( 'class' => 'form-control' ), 'required' => true))
            ->add('prenom', null, array('label' => 'Prénom', 'attr' => array( 'class' => 'form-control' ), 'required' => true))
            ->add('dateNaissance', null, array('label' => 'Date de naissance', 'attr' => array( 'class' => 'form-control', 'placeholder' => 'Format : dd/mm/aaaa' ), 'required' => true))
            ->add('telephone', IntegerType::class, array('label' => 'Tel. portable', 'attr' => array( 'class' => 'form-control' ), 'required' => true))
            ->add('email', EmailType::class, array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle', 'attr' => array( 'class' => 'form-control' ), 'required' => true))
            ->add('username', EmailType::class, array('label' => 'Confirmer l\'email', 'translation_domain' => 'FOSUserBundle', 'attr' => array( 'class' => 'form-control' ), 'required' => true))
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'options' => array('translation_domain' => 'FOSUserBundle', 'attr' => array( 'class' => 'form-control' )),
                'first_options' => array('label' => 'form.password'),
                'second_options' => array('label' => 'Confirmer le mot de passe :'),
                'invalid_message' => 'fos_user.password.mismatch',
            ))
            ;
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';
    }

    public function getBlockPrefix()
    {
        return 'app_user_registration';
    }

    // For Symfony 2.x
    public function getName()
    {
        return $this->getBlockPrefix();
    }

}
