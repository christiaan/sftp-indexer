<?php
namespace Christiaan\SftpIndexer\Infrastructure;

use Christiaan\SftpIndexer\Crawler\CrawledItem;
use Christiaan\SftpIndexer\Crawler\CrawlListener;

/**
 * SaveInDbListener
 * @author Christiaan Baartse <anotherhero@gmail.com>
 */
final class SaveInDbListener implements CrawlListener
{
    /** @var string */
    private $serverName;
    /** @var \PDO */
    private $db;
    private $lastSeen;
    /**
     * @var int
     */
    private $batchSize;

    public function __construct(\PDO $db, $serverName, $batchSize = 1500)
    {
        $this->lastSeen = time();
        $this->serverName = $serverName;
        $this->db = $db;

        $this->buffer = array();
        $this->batchSize = $batchSize;
    }

    public function onCrawledItem(CrawledItem $item)
    {
        $this->buffer[] = sprintf(
            '(%s, %s, %d, %d, %d)',
            $this->db->quote($this->serverName),
            $this->db->quote($item->getName()),
            $item->getSize(),
            $item->getFileMTime(),
            $this->lastSeen
        );
        if (count($this->buffer) === $this->batchSize) {
            $this->flushBuffer();
        }
    }

    public function __destruct()
    {
        if ($this->buffer) {
            $this->flushBuffer();
        }
    }

    private function flushBuffer()
    {
        if (!$this->buffer) {
            return;
        }
        $sql = 'REPLACE INTO `files` (`server`, `path`, `size`, `filemtime`, `last_seen`) VALUES ';
        $sql .= array_shift($this->buffer);
        while ($item = array_shift($this->buffer)) {
            $sql .= ', ' . $item;
        }

        $this->db->query($sql);
    }
}