<?php

namespace Glukose\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use FR3D\LdapBundle\Model\LdapUserInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser implements LdapUserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $name;
 
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $prenom;   

    /**
    * Ldap Object Distinguished Name
    * @ORM\Column(type="string", length=128)
    * @var string $dn
     */
    private $dn;

    
    public function __toString()
    {
        return $this->prenom.' '.$this->name;
        
    }
    public function __construct()
    {
        parent::__construct();
        if (empty($this->roles)) {
            $this->roles[] = 'ROLE_USER';
        }
    }

    public function setName($name) {
        $this->name = $name;
    }
 
    public function getName()
    {
        return $this->name;
    }
 
    public function setPrenom($prenom) {
        $this->prenom = $prenom;
    }
 
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * {@inheritDoc}
     */
    public function setDn($dn)
    {
        $this->dn = $dn;
    }

    /**
     * {@inheritDoc}
     */
    public function getDn()
    {
        return $this->dn;
    }
}
