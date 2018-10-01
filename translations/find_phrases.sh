#!/usr/bin/env bash

find .. -name '*.ph*' -exec grep -e "\->__(" {} + | sed 's/.*__("\(.*\)").*/\1/' | sort | uniq | while read -r word;do
    echo $word=$(echo $word | translate-bin -s google -f en -t $1 | sed 's/^.*>\(.*\)/\1/g')
done
