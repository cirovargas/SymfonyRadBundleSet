<?php
namespace Core\Bundle\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DumpSystemRolesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('Core:user:dump-roles')
            ->setDescription('Dump all roles generated in the permissions observer')

        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(
            implode(
                ',',
                array_flip($this->getContainer()->get('security.system_roles')->getRolesArray())
            )
        );
    }
}