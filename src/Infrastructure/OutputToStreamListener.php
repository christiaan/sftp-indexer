<?php
namespace Christiaan\SftpIndexer\Infrastructure;

use Christiaan\SftpIndexer\Crawler\CrawledItem;
use Christiaan\SftpIndexer\Crawler\CrawlListener;

/**
 * OutputToStreamListener
 * @author Christiaan Baartse <anotherhero@gmail.com>
 */
final class OutputToStreamListener implements CrawlListener
{
    /**
     * @var resource
     */
    private $stream;

    /**
     * @param resource $stream
     */
    public function __construct($stream)
    {
        if (!is_resource($stream)) {
            throw new \InvalidArgumentException('Stream argument is expected to be a resource.');
        }
        $this->stream = $stream;
    }

    public function onCrawledItem(CrawledItem $item)
    {
        $line = sprintf('%s', $item->getName()) . PHP_EOL;
        fwrite($this->stream, $line, strlen($line));
    }
}