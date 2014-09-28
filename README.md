Sftp Crawler and indexer
========================

Crawling
--------
Make sure to run `composer install` to get the dependencies and generate the autoloader files.

Add servers to the `app/config.yaml` file, copy the config.example.yaml to get started.

Then run the `servers:index-server` command with the configured *server-name*.

    php app/console.php servers:index-server <server-name>


Dependencies
------------

This project requires the PHP libssl module

Install on debian/ubuntu using apt-get

    sudo apt-get install libssh2-php