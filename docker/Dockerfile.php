FROM ezsystems/php:7.4
RUN apt update &&\
    apt install -y --no-install-recommends openssh-client gpg gpg-agent &&\
    rm -Rf /var/lib/apt/lists/*
ENV COMPOSER_MEMORY_LIMIT=-1
