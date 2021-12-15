# Enjolras
Enjolras c'est l'outil de vote des membres de La Chouette Coop construit avec **Symfony** 

Fonction
------------
Enjolras est une application symfony qui permet de créer et organiser des votes pour les prises de decisions aux seing de la chouette.

### Docker
Pour la 1ere exécution, il faut configurer la database :
```shell
cp .env.local .env
# Modifier le fichier .env avec les paramètres du LDAP, du mailer et de la BDD
cp docker-compose.yaml.dev docker-compose.yaml
docker-compose up -d db_enjolas
docker-compose exec db_enjolas bash
# Puis dans le docker `db_enjolas` :
mysql -p  # Enter the $MARIADB_ROOT_PASSWORD
CREATE USER 'enjolas'@'enjolas' IDENTIFIED BY 'enjolas';
GRANT ALL ON enjolas.* TO 'enjolas'@'enjolas';
# Vous pouvez quitter le docker
```

Pour restaurer un dump :
```shell
# put the backup.sql file in ./data
mysql -p enjolas < /var/lib/mysql/backup.sql
```
Une fois que la database est configurée :
```shell
docker-compose build enjolas
docker-compose up -d
```
