<?php

namespace Glukose\EnjolrasBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Glukose\EnjolrasBundle\Entity\Solution as Solution;

class SubjectAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'admin_subject';

    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper        
            ->add('title')
            ->add('subtitle')
            ->add('dateFin', 'sonata_type_date_picker', array(
                'required' => false,
                'dp_language'=>'fr',
                'format'=>'dd/MM/yyyy',
                'attr' => array(
                    'data-date-format' => 'DD/MM/YYYY',
                )
            ))
            ->add('voteSimple')
            ->add('description', 'textarea', array(
                'attr' => array('class' => 'ckeditor'), 
                'required' => false)
                 )

            ->add('termine', null, array('required' => false))
            ->add('visible')
            ->add('solutions', 'sonata_type_collection', array(
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
            ->add('visible')
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


    public function prePersist($subject) {
        $this->preUpdate($subject);           
    }

    public function preUpdate($subject) {

        //simple vote Init
        if($subject->getVoteSimple() && $subject->getSolutions()->isEmpty()){
            
            $subject->addSolution(new Solution('oui'));
            $subject->addSolution(new Solution('non'));
            $subject->addSolution(new Solution('ne se prononce pas'));

        }

        if($subject->getSolutions() != null){
            foreach($subject->getSolutions() as $solution){            
                $solution->setSubject($subject);
            }
        }
    }


}