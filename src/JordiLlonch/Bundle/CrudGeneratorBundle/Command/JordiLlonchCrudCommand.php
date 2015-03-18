<?php

/*
 * This file is part of the CrudGeneratorBundle
 *
 * It is based/extended from SensioGeneratorBundle
 *
 * (c) Jordi Llonch <llonch.jordi@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JordiLlonch\Bundle\CrudGeneratorBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineCrudCommand;
use JordiLlonch\Bundle\CrudGeneratorBundle\Generator\JordiLlonchCrudGenerator;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\Console\Input\InputArgument;


class JordiLlonchCrudCommand extends GenerateDoctrineCrudCommand
{
    protected $generator;
    protected $formGenerator;
    protected $template;

    protected function configure()
    {
        parent::configure();

        $this->setName('jordillonch:generate:crud');
        $this->setDescription('A CRUD generator with paginating and filters.');
        $this->addArgument(
                'template',
                InputArgument::OPTIONAL,
                'Choose the template folder'
            );
    }

    protected function createGenerator($bundle = null)
    {
        return new JordiLlonchCrudGenerator($this->getContainer()->get('filesystem'));
    }

    protected function getSkeletonDirs(BundleInterface $bundle = null)
    {
        $skeletonDirs = array();

        if (isset($bundle) && is_dir($dir = $bundle->getPath().'/Resources/SensioGeneratorBundle/skeleton/'.$this->template)) {
            $skeletonDirs[] = $dir;
        }

        if (is_dir($dir = $this->getContainer()->get('kernel')->getRootdir().'/Resources/SensioGeneratorBundle/skeleton/'.$this->template)) {
            $skeletonDirs[] = $dir;
        }

        $skeletonDirs[] = $this->getContainer()->get('kernel')->locateResource('@JordiLlonchCrudGeneratorBundle/Resources/skeleton/'.$this->template);
        $skeletonDirs[] = $this->getContainer()->get('kernel')->locateResource('@JordiLlonchCrudGeneratorBundle/Resources');

        return $skeletonDirs;
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getQuestionHelper();
        $dialog->writeSection($output, 'JordiLlonchCrudGeneratorBundle');

        parent::interact($input, $output);
    }
    
    protected function execute(InputInterface $input, OutputInterface $output){
        
        try{
            if(trim($input->getArgument('template')) != "" && !in_array($input->getArgument('template'),array('admin','external')))
                throw new \Exception("Invalid template folder");
        } catch (\Exception $ex) {

        }
        
        $this->template = trim($input->getArgument('template')) != "" ? $input->getArgument('template') :'admin' ;
        
        parent::execute($input, $output);
    }
}