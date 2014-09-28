<?php
namespace Christiaan\SftpIndexer\Cli;

use Symfony\Component\Yaml\Yaml;

/**
 * Application
 * @author Christiaan Baartse <anotherhero@gmail.com>
 */
final class Application extends \Symfony\Component\Console\Application
{
    public function __construct($configFile)
    {
        parent::__construct(
            'sftp-indexer',
            'dev'
        );

        $config = Yaml::parse(file_get_contents($configFile));
    }

    protected function getDefaultCommands()
    {
        $commands = parent::getDefaultCommands();

        return $commands;
    }
}
