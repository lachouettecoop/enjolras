<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),

            //User Bundle
            new FOS\UserBundle\FOSUserBundle(),
            //sonata Admin
            new Sonata\CoreBundle\SonataCoreBundle(),
            new Sonata\BlockBundle\SonataBlockBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),
            new Sonata\AdminBundle\SonataAdminBundle(),
            //Timestampable (doctrine extensions)
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            //Snappy html to pdf or html to img
            new Knp\Bundle\SnappyBundle\KnpSnappyBundle(),
            //pagination
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            //Images manipulatin
            new Liip\ImagineBundle\LiipImagineBundle(),
            //Comments on disqus
           // new Knp\Bundle\DisqusBundle\KnpDisqusBundle(),

            //LDAP
            new FR3D\LdapBundle\FR3DLdapBundle(),

            //My Bundles
            new Glukose\UserBundle\GlukoseUserBundle(),
            new Glukose\EnjolrasBundle\GlukoseEnjolrasBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
