<?php
namespace Christiaan\SftpIndexer;

/**
 * CrawlSftpServer
 * @author Christiaan Baartse <anotherhero@gmail.com>
 */
final class CrawlSftpServer
{
    /** @var CrawlListener[] */
    private $listeners;

    public function __construct($sftp)
    {
        $this->sftp = $sftp;
        $this->listeners = [];
    }

    public static function withPassword($host, $port, $username, $password)
    {
        $session = ssh2_connect($host, $port);
        ssh2_auth_password($session, $username, $password);

        $sftp = ssh2_sftp($session);
        return new CrawlSftpServer($sftp);
    }

    /**
     * @param CrawlListener $listener
     */
    public function addListener(CrawlListener $listener)
    {
        $this->listeners[] = $listener;
    }

    /**
     * Run the crawler
     * @return void
     */
    public function crawl()
    {
        $stack = array('.');

        while ($dir = array_shift($stack)) {
            try {
                $iterator = new \NoRewindIterator(new \DirectoryIterator("ssh2.sftp://{$this->sftp}/{$dir}"));
            } catch (\UnexpectedValueException $e) {
                continue;
            }
            /** @var \DirectoryIterator $entry */
            foreach ($iterator as $entry) {
                if ($entry->isDot() || !$entry->isReadable()) {
                    continue;
                }

                if ($entry->isDir()) {
                    $stack[] = str_replace(
                        "ssh2.sftp://{$this->sftp}/",
                        '',
                        $entry->getPathname()
                    );
                } else {
                    $this->indexFile($entry);
                }
            }
        }
    }

    private function indexFile(\SplFileInfo $entry)
    {
        $name = str_replace(
            "ssh2.sftp://{$this->sftp}/.",
            '',
            $entry->getPathname()
        );

        foreach ($this->listeners as $listener) {
            $listener->onCrawledItem(new CrawledItem($name, $entry->getSize(), $entry->getMTime()));
        }
    }
}