# 1. Usa come base l'immagine ufficiale con Apache e PHP 8.1
FROM php:8.1-apache

# 2. Copia TUTTI i file della mia cartella corrente sul Mac
#    e mettili dentro la cartella del server Apache nel container
COPY . /var/www/html/

# 3. Diciamo a Apache di rimanere in ascolto sulla porta 80
EXPOSE 80