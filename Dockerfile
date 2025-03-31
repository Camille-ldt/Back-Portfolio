# Utiliser une image PHP officielle sans Apache
FROM php:8.2-cli

# Copier les fichiers du projet dans le conteneur
WORKDIR /var/www/html

# Copier tous les fichiers du projet dans le conteneur
COPY . .

# Exposer le port 80
EXPOSE 80

# Commande pour démarrer le serveur PHP interne
CMD ["php", "-S", "0.0.0.0:80", "-t", "/var/www/html/api"]
