<?php
namespace Christiaan\SftpIndexer\Crawler;

interface CrawlListener
{
    public function onCrawledItem(CrawledItem $item);
}