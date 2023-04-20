ARG MYSQL_VERSION
FROM mysql:${MYSQL_VERSION}

LABEL maintainer="Aleksandr Drobotik <drobotiksbox@gmail.com>"

ARG TZ=UTC
ENV TZ ${TZ}
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone && chown -R mysql:root /var/lib/mysql/

COPY my.cnf.example /etc/mysql/conf.d/my.cnf

RUN chmod 0444 /etc/mysql/conf.d/my.cnf

CMD ["mysqld"]

ARG MYSQL_PORT
EXPOSE {$MYSQL_PORT}
