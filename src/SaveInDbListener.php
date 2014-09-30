<?php
namespace Christiaan\SftpIndexer;

/**
 * SaveInDbListener
 * @author Christiaan Baartse <anotherhero@gmail.com>
 */
final class SaveInDbListener implements CrawlListener
{
    /** @var string */
    private $serverName;
    /** @var \PDOStatement */
    private $preparedStatement;

    public function __construct(\PDO $db, $serverName)
    {
        $this->serverName = $serverName;

        $this->preparedStatement = $db->prepare('REPLACE INTO `files` (`server`, `path`, `size`, `filemtime`) VALUES("'.$serverName.'", :path, :size, :filemtime)');
    }

    public function onCrawledItem(CrawledItem $item)
    {
        $this->preparedStatement->execute(array(
                'path' => $item->getName(),
                'size' => $item->getSize(),
                'filemtime' => $item->getFileMTime(),
            ));
    }
}