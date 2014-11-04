<?php
namespace Christiaan\SftpIndexer\Cli;

use Christiaan\SftpIndexer\Infrastructure\OutputToStreamListener;
use Christiaan\SftpIndexer\Infrastructure\SaveInDbListener;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * IndexServerCommand
 * @author Christiaan Baartse <anotherhero@gmail.com>
 */
final class IndexServerCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('servers:index-server')
            ->addArgument('server-name', InputArgument::REQUIRED, 'Name of the server as found in the configuration file')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $serverName = $input->getArgument('server-name');

        /** @var Application $app */
        $app = $this->getApplication();

        $server = $app->getServerByName($serverName);

        $crawler = $app->getCrawlerForServer($server);

        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $crawler->addListener(new OutputToStreamListener(STDOUT));
        }

        $dbListener = new SaveInDbListener(
            $app->getDatabaseConnection(),
            $server->getName()
        );
        $crawler->addListener($dbListener);

        $crawler->crawl();

        return 0;
    }
}