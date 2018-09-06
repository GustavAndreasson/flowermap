#!/usr/bin/env bash

find .. -name '*.ph*' -exec grep -e "\->__(" {} + | sed 's/.*__("\(.*\)").*/\1=/' | sort | uniq
