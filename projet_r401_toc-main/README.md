## Lien vers notre site
https://r401frontend.alwaysdata.net/connexion.php

Identifiant: monsieur.entraineur@club.com
Mot de passe : entraineur123

## Configuration Apache
### MODs à installer
php
php-mysql
rewrite

### Configuration du virtual host
```
<VirtualHost *:80>
    ServerName ${serverName}
    DocumentRoot /var/www/${serverName}

    <Directory "/var/www/${serverName}">
        Options Indexes FollowSymLinks
        AllowOverride None
        Require all grantedd
    </Directory>

    RewriteEngine On
    RewriteCond %{REQUEST_URI} !\.(css|jpg)$
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^ index.php [QSA,L]
</VirtualHost>
```

## Technologies utilisées
- HTML
- CSS
- PHP
- PDO (pour la gestion de la base de données)
- MySQL

