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
docker-compose up -d db_enjolras
docker-compose exec db_enjolras bash
# Puis dans le docker `db_enjolras` :
mysql -p  # Enter the $MARIADB_ROOT_PASSWORD
CREATE USER 'enjolras'@'enjolras' IDENTIFIED BY 'enjolras';
GRANT ALL ON enjolras.* TO 'enjolras'@'enjolras';
# Vous pouvez quitter le docker
```

Pour restaurer un dump :
```shell
# put the backup.sql file in ./data
mysql -p enjolras < /var/lib/mysql/backup.sql
```
Une fois que la database est configurée :
```shell
docker-compose build enjolras
docker-compose up -d
```
