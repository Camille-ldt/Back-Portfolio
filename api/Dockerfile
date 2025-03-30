# Utiliser une image PHP officielle avec Apache
FROM php:8.2-apache

# Définir le dossier de travail
WORKDIR /var/www/html/api

# Copier tout le code du projet dans le conteneur
COPY api/ .

# Exposer le port 80
EXPOSE 80

# Lancer Apache au démarrage
CMD ["apache2-foreground"]
