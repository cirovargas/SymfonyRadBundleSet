<?php

namespace Core\Bundle\GeneratorBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineCrudCommand;
use Core\Bundle\GeneratorBundle\Generator\CoreCrudGenerator;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

class GenerateCoreCrudCommand extends GenerateDoctrineCrudCommand
{
    
    protected function configure()
    {
        parent::configure();
        
        $this
            ->setName('core:generate:crud')
            ->setDescription('Generate a improved crud based on SensioGeneratorBundle.')
        ;
    }
    
    protected function createGenerator($bundle = null)
    {
        return new CoreCrudGenerator(
                $this->getContainer()->get('filesystem'),
                $this->getContainer()->getParameter('kernel.root_dir')
            );
    }
    
    protected function getSkeletonDirs(BundleInterface $bundle = null)
    {
        $skeletonDirs = array();
        if (isset($bundle) && is_dir($dir = $bundle->getPath().'/Resources/SensioGeneratorBundle/skeleton')) {
            $skeletonDirs[] = $dir;
        }
        if (is_dir($dir = $this->getContainer()->get('kernel')->getRootdir().'/Resources/SensioGeneratorBundle/skeleton')) {
            $skeletonDirs[] = $dir;
        }
        $skeletonDirs[] = $this->getContainer()->get('kernel')->locateResource('@CoreGeneratorBundle/Resources/skeleton-pixel');
        $skeletonDirs[] = $this->getContainer()->get('kernel')->locateResource('@CoreGeneratorBundle/Resources');
        return $skeletonDirs;
    }
    
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if(method_exists($this, 'getDialogHelper') ) {
            $dialog = $this->getDialogHelper();
        } else {
            $dialog = $this->getQuestionHelper();
        }
        
        $dialog->writeSection($output, '@CoreGeneratorBundle');
        parent::interact($input, $output);
    }
}
