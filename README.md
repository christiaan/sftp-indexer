Sftp Crawler and indexer
========================

Crawling
--------

    <?php
    use Christiaan\SftpIndexer\OutputToStreamListener;

    require 'vendor/autoload.php';

    $crawler = \Christiaan\SftpIndexer\CrawlSftpServer::withPassword(
        'example.com',
        2222,
        'username',
        'password'
    );

    $crawler->addListener(new OutputToStreamListener(STDOUT));


    $crawler->crawl();


Dependencies
------------

This project requires the PHP libssl module

Install on debian/ubuntu using apt-get

    sudo apt-get install libssh2-php