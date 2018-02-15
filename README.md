# Récuperer les sources

    git clone

# Changer la configuration

    cp docker-compose.yml.dist docker-compose.yml
    nano docker-compose.yml

 - changer db.environment.MYSQL_PASSWORD
 - changer nginx.environment.VIRTUAL_HOST

# Lancer la stack

    docker-compose up -d

# Installer les dependances de l'application php avec composer

    docker run -ti --rm -v $(pwd)/symfony:/app -v ~/.composer:/root/composer composer/composer:php7 install

Répondre aux questions:

    Some parameters are missing. Please provide them.
    database_driver (pdo_mysql):
    database_host (127.0.0.1): db
    database_port (null): 3306
    database_name (symfony):
    database_user (root): symfony
    database_password (null): <votre mdp db, MYSQL_PASSWORD dans docker-compose.yml >
    mailer_transport (smtp):
    mailer_host (127.0.0.1):
    mailer_user (null):
    mailer_password (null):
    locale (fr):
    secret (ThisTokenIsNotSoSecretChangeIt):




    chmod -R 777 symfony/app/cache/ logs/
    chmod +x symfony/app/console

# Creer le schema de base de donnée

    docker-compose run —rm php app/console doctrine:schema:create
    docker-compose run —rm php app/console assets:install —symlink —relative
    docker-compose run —rm php app/console fos:user:create adminuser —super-admin



# composer.phar install
# rajouter dans parameters.yml
ldapserveradress: ldap.lachouettecoop.fr
* ldapuser: cn=admin,dc=lachouettecoop,dc=fr
* ldapmdp: <changeme>

    sudo docker exec -it dockersymfony_php_1 /bin/bash
    php composer.phar install (database_host: db, database_port: 3306 etc…)
    php app/console doctrine:schema:create
    chmod -R 777 app/cache
    chmod -R 777 app/logs
    rajouter un super admin : php app/console fos:user:create adminuser —super-admin
    sudo docker-compose run --rm php app/console fos:user:promote ccel781@gmail.com ROLE_ADMIN #attention c'est - - rm
