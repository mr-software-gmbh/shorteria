#!/bin/bash
netstat -an | grep 13333 > /dev/null; if [ 0 != $? ]; then exit 1; fi
