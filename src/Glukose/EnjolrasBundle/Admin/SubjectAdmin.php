<?php

namespace Glukose\EnjolrasBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class SubjectAdmin extends Admin
{
    protected $baseRouteName = 'admin_subject';

    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper        
            ->add('title')
            ->add('subtitle')            
            ->add('description', 'textarea', array(
                'attr' => array('class' => 'ckeditor'), 
                'required' => false)
                 )
            ->add('termine', null, array('required' => false))
            ->add('solutions', 'sonata_type_collection', array(
                'cascade_validation' => true,
                'required' => false,
            ), array(
                'edit'              => 'inline',
                'inline'            => 'table',
                'sortable'          => 'position',
                //'link_parameters'   => array('context' => $context),
                'admin_code'        => 'sonata.admin.solution'
            ))
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
    
    public function preUpdate($subject) {
        if($subject->getSolutions() != null){
            foreach($subject->getSolutions() as $solution){
                //on établi la relation entre ouvrage et la table intérmédiaire autre participant
                $solution->setSubject($subject);
            }
        }
    }
       

}