FROM bitnami/symfony:4.4

ENV COMPOSER_MEMORY_LIMIT=-1

WORKDIR /app/
COPY . .
RUN composer install
