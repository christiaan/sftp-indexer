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
Instead of having the whole filesystem crawled every time the index is refreshed a sftp server can also provide a `sftp-indexer-index.gz` file.
When that file is found it is used instead, this greatly reduces load on both ends.

Create a shell script `/root/sftpindexer.sh` containing the following

```bash
#!/bin/bash
set -e
cd $1
find . -printf "%y\t%P\t%s\t%T@\n" | gzip -c > sftp-indexer-index-indexing.gz
mv sftp-indexer-index-indexing.gz sftp-indexer-index.gz
```

Crontab the shellscript like so

    15 6 * * * /root/sftpindexer.sh /directory/to/index


Every directory the crawler enters is first checked for a `sftp-indexer-index.gz` file.
So it is possible to have multiple indexes which have different refresh intervals.

    # Music doesn't update much so only update every 3 days
    15 6 */3 * * /root/sftpindexer.sh /mnt/data/public/music
    # Video contains series which are added every day during the night
    15 6 * * * /root/sftpindexer.sh /mnt/data/public/video
