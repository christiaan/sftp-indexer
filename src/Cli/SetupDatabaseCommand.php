<?php
namespace Christiaan\SftpIndexer\Cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * SetupDatabaseCommand
 * @author Christiaan Baartse <anotherhero@gmail.com>
 */
final class SetupDatabaseCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('setup-database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Application $app */
        $app = $this->getApplication();

        $db = $app->getDatabaseConnection();

    }
}