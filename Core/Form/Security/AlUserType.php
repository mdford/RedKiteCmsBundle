<?php

namespace AlphaLemon\AlphaLemonCmsBundle\Core\Form\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\MinLength;
use Symfony\Component\Validator\Constraints\Collection;

class AlUserType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('id', 'hidden');
        $builder->add('username');
        $builder->add('password');
        $builder->add('email');  
        
        $builder->add('AlRole', 'model', array(
            'class'     => 'AlphaLemon\AlphaLemonCmsBundle\Model\AlRole',
            'property'  => 'Role',
        ));
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'AlphaLemon\AlphaLemonCmsBundle\Model\AlUser',
            'csrf_protection' => false,
        );
    }

    public function getName()
    {
        return 'al_user';
    }
}