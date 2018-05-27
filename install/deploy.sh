#!/bin/bash

rsync -avze ssh --exclude install --exclude var --exclude *~ ../* gustav@192.168.1.220:/var/www/html/flowermap/
