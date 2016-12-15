#!/bin/bash

SCRIPT_PATH=`dirname $(readlink -f "$0")`
LAABS_PATH="$SCRIPT_PATH/../../../";

cd $SCRIPT_PATH

source 0-config.sh

# Run test job
php cli.php UPDATE batchProcessing/scheduling/Process -tokenfile:"$SCRIPT_PATH/0-token.txt"
