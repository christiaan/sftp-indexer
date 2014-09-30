Sftp server indexer
===================

Indexing
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

Generate index on server using crontab and find
-----------------------------------------------
Instead of having the whole filesystem crawled every time the index is refreshed a sftp server can also provide a `sftp-indexer-index.gz` file in the root of the filesystem.
When that file is found it is used instead, this greatly reduces load on both ends.

    cd /path/to/sftp/chroot && find . -printf "%P\t%s\t%T@\n" | gzip -c > sftp-indexer-index.gz