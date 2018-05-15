<?php

namespace Glukose\EnjolrasBundle\Admin;


use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class SolutionAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'admin_solution';

    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper        
            ->add('title')
            ->add('description', 'textarea', array(
                'attr' => array('class' => 'ckeditor'), 
                'required' => false)
                 )
            ->add('pros', 'textarea', array(
                'attr' => array('class' => 'ckeditor'), 
                'label' => 'Pour', 
                'required' => false)
                 )
            ->add('cons', 'textarea', array(
                'attr' => array('class' => 'ckeditor'),
                'label' => 'Contre', 
                'required' => false)
                 )
            ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title')            
            ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->addIdentifier('title')
            ;
    }
       

}