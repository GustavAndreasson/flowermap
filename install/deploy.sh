#!/bin/bash

rsync -avze ssh --exclude install --exclude var --exclude *~ --exclude .git ../ gustav@192.168.1.220:/var/www/html/flowermap/
