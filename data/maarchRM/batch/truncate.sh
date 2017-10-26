#!/bin/sh

SCRIPT_PATH=`dirname $(readlink -f "$0")`;
cd $SCRIPT_PATH;

BUNDLE_PATH="$SCRIPT_PATH/../../../src/bundle/";
DATA_SQL_PATH="$SCRIPT_PATH/../sql/";

# 1 - Creation des tables et schémas SQL
cd $BUNDLE_PATH;
su postgres -c "psql -p '5432' 'maarchRM' < ../../data/maarchRM/sql/psql/app_truncate.pgsql.sql";
