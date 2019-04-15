FROM debian:9.8

LABEL authors="Alexandre Morin <alexandre.morin@maarch.org>"

ENV POSTGRES_DB="workflow"
ENV POSTGRES_USER="maarch"
ENV POSTGRES_PASSWORD="maarch"

ENV APP_VERSION="release/2.4"
ENV POSTGRES_VERSION="9.6"

RUN apt-get update -y && apt-get upgrade -y && apt-get install -y \
    apache2 \
    php \
    php-pgsql \
    php-pgsql \
    php-xml \
    php-cli \
    php-common \
    openssl \
    default-jre \
    git \
    p7zip-full \
    postgresql \
    postgresql-client \
    libapache2-mod-php \
    libreoffice-writer \
    && apt-get clean

RUN sed -i -e 's/# en_US.UTF-8 UTF-8/fr_FR.UTF-8 UTF-8/' /etc/locale.gen && \
    locale-gen

ENV LANG fr_FR.UTF-8
ENV LANGUAGE fr_FR
ENV LC_ALL fr_FR.UTF-8

WORKDIR /var/www/
RUN git clone --depth 1 -b $APP_VERSION https://labs.maarch.org/maarch/maarchRM.git laabs \
    && cd laabs/src/ext \
    && git clone --depth 1 -b $APP_VERSION https://labs.maarch.org/maarch/workflow.git  \
    && cp workflow/data/conf/vhost.conf.default workflow/data/conf/vhost.conf \
    && cp workflow/data/conf/configuration.ini.default workflow/data/conf/configuration.ini \
    && cp workflow/data/conf/confvars.ini.default workflow/data/conf/confvars.ini \
    && useradd -m -g www-data maarch \
    && export APACHE_RUN_USER=maarch \
    && a2enmod rewrite \
    && a2enmod env \
    && mkdir -p /var/www/laabs/data/maarchRM/repository/archives_1 /var/www/laabs/data/maarchRM/repository/archives_2 \
    && chown -R maarch:www-data /var/www \
    && chmod -R 775 /var/www

USER postgres

RUN /etc/init.d/postgresql start \
    && psql --command "CREATE USER $POSTGRES_USER;" \
    && psql --command "ALTER ROLE $POSTGRES_USER WITH CREATEDB;" \
    && psql --command "ALTER ROLE $POSTGRES_USER WITH SUPERUSER;" \
    && psql --command "ALTER USER $POSTGRES_USER WITH ENCRYPTED PASSWORD '$POSTGRES_PASSWORD';" \
    && psql --command "CREATE DATABASE \"$POSTGRES_DB\" WITH OWNER $POSTGRES_USER;" \
    && echo "host all  all    127.0.0.1/32  trust" >> /etc/postgresql/$POSTGRES_VERSION/main/pg_hba.conf \
    && export PGPASSWORD=$POSTGRES_PASSWORD \
    && /var/www/laabs/src/ext/workflow/data/batch/pgsql/schema.sh \
    && /var/www/laabs/src/ext/workflow/data/batch/pgsql/data.sh

EXPOSE 80

USER root

WORKDIR /etc/apache2/sites-available/
RUN touch maarchRM.conf \
    && echo "Include /var/www/laabs/src/ext/workflow/data/conf/vhost.conf" >> maarchRM.conf \
    && a2ensite maarchRM.conf \
    && a2dissite 000-default.conf

RUN touch /var/www/run.sh \
    && echo "service postgresql start" >> /var/www/run.sh \
    && echo "service cron start" >> /var/www/run.sh \
    && chmod +x /var/www/run.sh \
    && echo "/usr/sbin/apache2ctl -D FOREGROUND" >> /var/www/run.sh \
    && echo "* * * * * root /var/www/laabs/data/maarchRM/batch/scheduling.sh" >> /etc/crontab \
    && sed -i -e 's!phdF9WkJuTKkDuPXoqDZuOjLMAFGC6ZrzrSEEqC9YjIbAus7MI6fyfa0kl79fDzjBCqmdKA7jNkRa+Q1eC0Itdcvvsrj2qyT3oVI/LOBBBDq7Ewklpi8mQ==!RJpzB36bmR+iuz/aHN9Zl9PDn8tZEs4mzsz9OXNeNIrej2+v3UMzAsF3PSzDUlZ73kPvgqbQmZvza0eZO062uQu57Rdah9z3mdbTh6NBiiR8FQTnW6eVgQ==!' /var/www/laabs/src/ext/workflow/data/batch/0-token.txt \
    && sed -i -e 's!phdF9WkJuTKkDuPXoqDZuOjLMAFGC6ZrqwSFnXohEKT1a0AUcIDxGYN1StxaeuLt99/FWvq0zY4qCBZQSFyXXIO9KGYpbXjodhj/r7YyGLxBXjtcIpiGaLQzyq3L5sqES9iVRSy1+1k=!RJpzB36bmR+iuz/aHN9Zl9PDn8tZEs4mzsz9ORUXZpbMim/ilUMpE9FzYG3TW0Eii0Oy1PaFyJ35aBqcMU3gvAq4v0ZY0Z/r0cPVzbAaymd1UEnsAe3MjqGLt7BxvxiHJQ==!' /var/www/laabs/src/ext/workflow/data/batch/0-depositToken.txt
    #&& sed -i -e 's!phdF9WkJuTKkDuPXoqDZuOjLMAFGC6ZrqwSFnXohEKT1a0AUcIDxGYN1StxaeuLt99/FWvq0zY4qCBZQSFyXXIO9KGYpbXjodhj/r7YyGLxBXjtcIpiGaLQzyq3L5sqES9iVRSy1+1k=!RJpzB36bmR+iuz/aHN9Zl9PDn8tZEs4mzsz9ORUXZpbMim/ilUMpE9FzYG3TW0Eii0Oy1PaFyJ35aBqcMU3gvAq4v0ZY0Z/r0cPVzbAaymd1UEnsAe3MjqGLt7BxvxiHJQ==!' /var/www/laabs/src/ext/workflow/data/batch/0-depositToken.txt \
    #&& echo "/var/www/laabs/src/ext/workflow/data/batch/import.sh /var/www/laabs/src/ext/workflow/data/samples/jeu_demo_24/FAC_FOUR_DAF/ /var/www/laabs/src/ext/workflow/data/samples/jeu_demo_24/FAC_FOUR_DAF/fac_four_daf.txt"  >> /var/www/run.sh\
    #&& echo "/var/www/laabs/src/ext/workflow/data/batch/import.sh /var/www/laabs/src/ext/workflow/data/samples/jeu_demo_24/FAC_FOUR_DSI/ /var/www/laabs/src/ext/workflow/data/samples/jeu_demo_24/FAC_FOUR_DSI/fac_four_dis.txt" >> /var/www/run.sh \
    #&& echo "/var/www/laabs/src/ext/workflow/data/batch/import.sh /var/www/laabs/src/ext/workflow/data/samples/jeu_demo_24/FAC_FOUR_MKT/ /var/www/laabs/src/ext/workflow/data/samples/jeu_demo_24/FAC_FOUR_MKT/fac_four_mkt.txt" >> /var/www/run.sh \
    #&& echo "/var/www/laabs/src/ext/workflow/data/batch/import.sh /var/www/laabs/src/ext/workflow/data/samples/jeu_demo_24/FAC_FOUR_SG/ /var/www/laabs/src/ext/workflow/data/samples/jeu_demo_24/FAC_FOUR_SG/fac_four_sg.txt" >> /var/www/run.sh

CMD /var/www/run.sh
