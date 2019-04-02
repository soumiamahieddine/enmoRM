#!/bin/bash

[[ ! -e /.dockerenv ]] && exit 0

set -xe

apt-get install postgresql-client -yqq

psql -h "postgres" -U "$POSTGRES_USER" -d "$POSTGRES_DB" -w < /builds/maarch/maarchRM/data/maarchRM/batch/pgsql/schema.sh
psql -h "postgres" -U "$POSTGRES_USER" -d "$POSTGRES_DB" -w < /builds/maarch/maarchRM/data/maarchRM/batch/pgsql/data_fr.sh
