#!/bin/bash

SCRIPT_PATH=`dirname $(readlink -f "$0")`
LAABS_PATH="$SCRIPT_PATH/../../../";

cd $SCRIPT_PATH

source 0-config.sh

# Run test job
php cli.php READ recordsManagement/archiveCompliance/periodic -tokenfile:"$SCRIPT_PATH/0-token.txt" limit="10" delay="PT1M"