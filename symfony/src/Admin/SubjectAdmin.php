<?php

namespace App\Admin;

use App\Entity\Subject;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use App\Entity\Solution as Solution;
use Sonata\CoreBundle\Form\Type\CollectionType;
use Sonata\CoreBundle\Form\Type\DatePickerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class SubjectAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'admin_subject';

    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper        
            ->add('title')
            ->add('subtitle')

            ->add('datePleniere', DatePickerType::class, array(
                'required' => false,
                'label' => 'Date de la plénière (début des coms)',
                'dp_language'=>'fr',
                'format'=>'dd/MM/yyyy',
                'attr' => array(
                    'data-date-format' => 'DD/MM/YYYY',
                )
            ))
            ->add('dateDebut', DatePickerType::class, array(
                'required' => false,
                'label' => 'Date de début du vote (après les commentaires)',
                'dp_language'=>'fr',
                'format'=>'dd/MM/yyyy hh:mm a',
                'attr' => array(
                    'data-date-format' => 'DD/MM/YYYY hh:mm a',
                )
            ))
            ->add('dateFin', DatePickerType::class, array(
                'required' => false,
                'dp_language'=>'fr',
                'format'=>'dd/MM/yyyy hh:mm a',
                'attr' => array(
                    'data-date-format' => 'DD/MM/YYYY hh:mm a',
                )
            ))
            ->add('voteSimple')
            ->add('description', TextareaType::class, array(
                'attr' => array('class' => 'ckeditor'), 
                'required' => false)
                 )

            ->add('termine', null, array('required' => false))
            ->add('visible')
            ->add('anonyme')
            ->add('solutions', CollectionType::class, array(
                'required' => false,
            ), array(
                'edit'              => 'inline',
                'inline'            => 'table',
                'sortable'          => 'position',
                //'link_parameters'   => array('context' => $context),
                'admin_code'        => 'sonata.admin.solution'
            ))
            ->add('arguments', CollectionType::class, array(
                'required' => false,
            ), array(
                'edit'              => 'inline',
                'inline'            => 'table',
                'sortable'          => 'position',
                //'link_parameters'   => array('context' => $context),
                'admin_code'        => 'admin.argument'
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
        /** @var Subject $subject */
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

        if($subject->getArguments() != null){
            foreach($subject->getArguments() as $argument){
                $argument->setSubject($subject);
            }
        }
    }


}