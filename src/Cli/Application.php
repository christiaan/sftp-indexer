<?php
namespace Christiaan\SftpIndexer\Cli;

use Christiaan\SftpIndexer\SftpServer;
use Christiaan\SftpIndexer\SftpServerCrawler;
use PDO;
use Symfony\Component\Yaml\Yaml;

/**
 * Application
 * @author Christiaan Baartse <anotherhero@gmail.com>
 */
final class Application extends \Symfony\Component\Console\Application
{
    /** @var array */
    private $config;

    public function __construct($configFile)
    {
        parent::__construct(
            'sftp-indexer',
            'dev'
        );

        $this->config = Yaml::parse(file_get_contents($configFile));
    }

    protected function getDefaultCommands()
    {
        $commands = parent::getDefaultCommands();

        $commands[] = new IndexServerCommand();

        return $commands;
    }

    /**
     * @return PDO
     */
    public function getDatabaseConnection()
    {
        $db = new PDO(
            $this->config['index']['dsn'],
            $this->config['index']['user'],
            $this->config['index']['password'],
            [
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'',
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        );

        return $db;
    }

    /**
     * @return SftpServer[]
     */
    public function getServers()
    {
        $servers = [];
        foreach ($this->config['servers'] as $name => $server) {

            $servers[] = new SftpServer(
                $name,
                $server['host'],
                $server['port'],
                $server['user'],
                $server['password']
            );
        }

        return $servers;
    }

    /**
     * @param string $name
     * @throws \InvalidArgumentException on incorrect name
     * @return SftpServer
     */
    public function getServerByName($name)
    {
        if (!array_key_exists($name, $this->config['servers'])) {
            throw new \InvalidArgumentException('Unknown server ' . $name);
        }

        $server = $this->config['servers'][$name];

        return new SftpServer(
            $name,
            $server['host'],
            $server['port'],
            $server['user'],
            $server['password']
        );
    }

    /**
     * @param SftpServer $server
     * @return SftpServerCrawler
     */
    public function getCrawlerForServer(SftpServer $server)
    {
        return SftpServerCrawler::withPassword(
            $server->getHost(),
            $server->getPort(),
            $server->getUser(),
            $server->getPassword()
        );
    }
}
