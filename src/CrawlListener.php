<?php
namespace Christiaan\SftpIndexer;

interface CrawlListener
{
    public function onCrawledItem(CrawledItem $item);
}