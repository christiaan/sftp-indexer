<?php
namespace Christiaan\SftpIndexer\Crawler;

/**
 * SftpServerCrawler
 * @author Christiaan Baartse <anotherhero@gmail.com>
 */
final class SftpServerCrawler
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
        return new SftpServerCrawler($sftp);
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
            if ($this->tryReadIndexFile($dir)) {
                continue;
            }

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

        $item = new CrawledItem($name, $entry->getSize(), $entry->getMTime());
        foreach ($this->listeners as $listener) {
            $listener->onCrawledItem($item);
        }
    }

    private function tryReadIndexFile($dir)
    {
        $error = false;
        try {
            $indexFile = @fopen(
                "ssh2.sftp://{$this->sftp}/{$dir}/sftp-indexer-index.gz",
                'rb'
            );
            $tempFile = tempnam('/tmp', 'sftp-indexer');
            $tempStream = @fopen($tempFile, 'w');

            stream_copy_to_stream($indexFile, $tempStream);
        } catch (\ErrorException $e) {
            $error = true;
        }

        if (!empty($indexFile)) fclose($indexFile);
        if (!empty($tempStream)) fclose($tempStream);
        if ($error) {
            if (isset($tempFile)) unlink($tempFile);
            return false;
        }

        foreach (new \SplFileObject('compress.zlib://'.$tempFile, 'r') as $line) {
            $this->indexCacheLine($line);
        }

        unlink($tempFile);
        return true;
    }

    private function indexCacheLine($line)
    {
        $line = explode("\t", $line, 4);
        if (count($line) === 4 && $line[0] === 'f') {
            $item = new CrawledItem($line[1], $line[2], $line[3]);
            foreach ($this->listeners as $listener) {
                $listener->onCrawledItem($item);
            }
        }
    }
}