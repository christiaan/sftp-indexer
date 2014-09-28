<?php
namespace Christiaan\SftpIndexer;

/**
 * CrawledItem
 * @author Christiaan Baartse <anotherhero@gmail.com>
 */
final class CrawledItem
{
    /** @var string  */
    private $name;
    /** @var int  */
    private $size;
    /** @var int  */
    private $fileMTime;

    /**
     * @param string $name
     * @param int $size
     * @param int $fileMTime
     */
    public function __construct($name, $size, $fileMTime)
    {
        $this->name = (string) $name;
        $this->size = (int) $size;
        $this->fileMTime = (int) $fileMTime;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}