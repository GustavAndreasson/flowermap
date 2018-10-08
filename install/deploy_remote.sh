#!/bin/bash

rsync -avze ssh --exclude install --exclude var --exclude *~ --exclude .git ../ gustav@212.85.79.94:/var/www/html/flowermap/
