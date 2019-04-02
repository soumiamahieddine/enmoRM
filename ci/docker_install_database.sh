#!/bin/bash

[[ ! -e /.dockerenv ]] && exit 0

set -xe

apt-get install postgresql-client -yqq

psql -h "postgres" -U "$POSTGRES_USER" -d "$POSTGRES_DB" -w < /builds/maarch/maarchRM/src/bundle/audit/Resources/sql/schema.pgsql.sql
psql -h "postgres" -U "$POSTGRES_USER" -d "$POSTGRES_DB" -w < /builds/maarch/maarchRM/src/bundle/auth/Resources/sql/schema.pgsql.sql
psql -h "postgres" -U "$POSTGRES_USER" -d "$POSTGRES_DB" -w < /builds/maarch/maarchRM/src/bundle/batchProcessing/Resources/sql/schema.pgsql.sql
psql -h "postgres" -U "$POSTGRES_USER" -d "$POSTGRES_DB" -w < /builds/maarch/maarchRM/src/bundle/contact/Resources/sql/schema.pgsql.sql
psql -h "postgres" -U "$POSTGRES_USER" -d "$POSTGRES_DB" -w < /builds/maarch/maarchRM/src/bundle/digitalResource/Resources/sql/schema.pgsql.sql
psql -h "postgres" -U "$POSTGRES_USER" -d "$POSTGRES_DB" -w < /builds/maarch/maarchRM/src/bundle/filePlan/Resources/sql/schema.pgsql.sql
psql -h "postgres" -U "$POSTGRES_USER" -d "$POSTGRES_DB" -w < /builds/maarch/maarchRM/src/bundle/lifeCycle/Resources/sql/schema.pgsql.sql
psql -h "postgres" -U "$POSTGRES_USER" -d "$POSTGRES_DB" -w < /builds/maarch/maarchRM/src/bundle/organization/Resources/sql/schema.pgsql.sql
psql -h "postgres" -U "$POSTGRES_USER" -d "$POSTGRES_DB" -w < /builds/maarch/maarchRM/src/bundle/recordsManagement/Resources/sql/schema.pgsql.sql

psql -h "postgres" -U "$POSTGRES_USER" -d "$POSTGRES_DB" -w < /builds/maarch/maarchRM/data/maarchRM/sql/pgsql/demo.sql
