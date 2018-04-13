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

    docker run -ti --rm --user $(id -u):$(id -g) -v $(pwd)/symfony:/app -v ~/.composer:/root/composer composer install --ignore-platform-reqs

NOTE(pht): ça, ou quelque chose du genre debian

    docker-compose run --rm php composer install

(Mais ça fait exploser la mémoire sur ma machine :/ )

Répondre aux questions (laisser les valeurs par défaut pour les autres):

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

# Creer le schema de base de donnée

    chmod +x symfony/bin/console
    docker-compose run --rm php bin/console doctrine:schema:create
    docker-compose run --rm php bin/console assets:install --symlink
    docker-compose run --rm php bin/console fos:user:create adminuser --super-admin


# composer.phar install
# rajouter dans parameters.yml

    ldapHost: ldap.lachouettecoop.fr
    ldapAdminUser: cn=admin,dc=lachouettecoop,dc=fr
    ldapAdminPass: <changeme>

    sudo docker exec -it dockersymfony_php_1 /bin/bash
    php composer.phar install (database_host: db, database_port: 3306 etc…)
    php app/console doctrine:schema:create
    chmod -R 777 symfony/var/cache
    chmod -R 777 symfony/var/logs
    
    sudo docker-compose run --rm php app/console fos:user:promote ccel781@gmail.com ROLE_ADMIN #attention c'est - - rm
