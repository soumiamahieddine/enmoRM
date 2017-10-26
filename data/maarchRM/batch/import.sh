#!/bin/bash

SCRIPT_PATH=`dirname $(readlink -f "$0")`
LAABS_PATH="$SCRIPT_PATH/../../../";

cd $SCRIPT_PATH

source 0-config.sh

if [ $# -eq 0 ]
  then
    echo "Aucun argument entré"
    echo "RAPPEL : 1 er argument -> chemin du repertoire de documents -- 2 ème argument -> chemin vers le fichier de description"
    exit 1
fi

if [ $# -eq 1 ]
  then
    echo "1 seul argument entré"
    echo "RAPPEL : 1 er argument -> chemin du repertoire de documents -- 2 ème argument -> chemin vers le fichier de description"
    exit 1
fi

# Run test job
php cli.php CREATE recordsManagement/archive/archiveBatch batchDirectory="$1" descriptionFilePath="$2" -tokenfile:"../data/maarchRM/batch/0-token.txt" -accept:"application/json"
