#!/bin/bash

[[ ! -e /.dockerenv ]] && exit 0

set -xe

apt-get install postgresql-client -yqq

psql -h "postgres" -U "$POSTGRES_USER" -d "$POSTGRES_DB" -w < /builds/maarch/MaarchRM/data/maarchRM/batch/psql/structure.sql
psql -h "postgres" -U "$POSTGRES_USER" -d "$POSTGRES_DB" -w < /builds/maarch/MaarchRM/data/maarchRM/batch/psql/data_fr.sql
