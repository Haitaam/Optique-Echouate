# Guide d'hébergement — Optique Échouate

## Prérequis serveur

- **PHP** ≥ 8.3 avec extensions : `curl`, `gd`, `mbstring`, `pdo_mysql`, `xml`, `bcmath`, `json`, `fileinfo`
- **MySQL** ≥ 8.0 ou **MariaDB** ≥ 10.4
- **Node.js** ≥ 20 (uniquement pour le build des assets, peut être retiré après)
- **Composer** ≥ 2.5
- **Apache** (mod_rewrite activé) ou **Nginx**

---

## 1. Déploiement rapide

```bash
# 1. Cloner le projet
git clone <votre-repo> optique-echouate
cd optique-echouate

# 2. Configurer l'environnement
cp .env.example .env
nano .env   # Modifier DB, APP_URL, APP_KEY...

# 3. Générer la clé d'application
php artisan key:generate

# 4. Installer les dépendances
composer install --no-dev --optimize-autoloader

# 5. Build des assets (optionnel si fait en local)
npm ci && npm run build

# 6. Créer la base de données et migrer
mysql -u root -p -e "CREATE DATABASE optique_echouate CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php artisan migrate --force

# 7. Lien symbolique pour le stockage
php artisan storage:link

# 8. Optimiser Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Commande tout-en-un
```bash
composer run deploy
```

> ⚠️ Avant d'exécuter `composer run deploy`, assurez-vous que `.env` est configuré et la base de données créée.

---

## 2. Configuration du serveur Web

### Apache

Le fichier `public/.htaccess` est fourni par Laravel. Créez un VirtualHost :

```apache
<VirtualHost *:80>
    ServerName optique-echouate.com
    DocumentRoot /var/www/optique-echouate/public

    <Directory /var/www/optique-echouate/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/optique-echouate-error.log
    CustomLog ${APACHE_LOG_DIR}/optique-echouate-access.log combined
</VirtualHost>
```

```bash
sudo a2ensite optique-echouate
sudo systemctl reload apache2
```

### Nginx

```nginx
server {
    listen 80;
    server_name optique-echouate.com;
    root /var/www/optique-echouate/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## 3. Base de données

### Création

```sql
CREATE DATABASE optique_echouate
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
```

### Configuration `.env`

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=optique_echouate
DB_USERNAME=votre_utilisateur
DB_PASSWORD=votre_mot_de_passe
```

### Synchronisation des produits

Après avoir placé les images dans `public/images/glasses/{categorie}/` :

```bash
php artisan glasses:sync
```

> Les images doivent être présentes sur le serveur — le sync lit le disque, pas une URL.

---

## 4. Images des produits

Structure de dossiers :

```
public/images/glasses/
├── men/
├── women/
├── sunglasses/
│   ├── men/
│   └── women/
└── luxury/
```

Transférez vos images par FTP/SCP dans les dossiers appropriés, puis lancez :

```bash
php artisan glasses:sync
```

---

## 5. Maintenance courante

```bash
# Vider le cache
php artisan optimize:clear

# Cache de configuration (production uniquement)
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Re-sync des produits
php artisan glasses:sync

# Voir les produits orphelins (dry-run)
php artisan glasses:sync --dry-run
```

---

## 6. Sécurité

### .env — ne jamais commiter
Le fichier `.env` contient les mots de passe et la clé APP_KEY. Il est déjà dans `.gitignore`.

### Permissions

```bash
# Serveur partagé
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Vérifier que public/images/glasses est lisible
chmod -R 755 public/images/glasses
```

### HTTPS

Avec Certbot (Let's Encrypt) :

```bash
sudo apt install certbot python3-certbot-apache   # ou -nginx
sudo certbot --apache -d optique-echouate.com
sudo certbot renew --dry-run
```

---

## 7. Synchronisation automatique des produits

### Cron pour sync quotidien

```cron
0 3 * * * cd /var/www/optique-echouate && php artisan glasses:sync >> storage/logs/sync.log 2>&1
```

### Auto-push Git (post-commit)

```bash
# Créer le hook
cat > .git/hooks/post-commit << 'EOF'
#!/bin/bash
git push origin master
EOF
chmod +x .git/hooks/post-commit
```

---

## 8. Hébergeurs compatibles

| Hébergeur        | Compatibilité | Remarque                                        |
|------------------|---------------|-------------------------------------------------|
| **Shared Hosting** (cPanel, OVH, Hostinger) | ✅ Complète | Upload via FTP. Imposer PHP 8.3+ en cPanel. |
| **VPS** (DigitalOcean, Linode, Hetzner)     | ✅ Idéale   | Installation libre. Optimisé pour production.  |
| **Platform.sh**  | ✅            | Déploiement Git natif. Build inclus.           |
| **Laravel Forge**| ✅ Excellent  | Gère Nginx, queue, cron, SSL automatiquement.  |
| **Railway**      | ✅            | Déploiement facile. Build automatique.         |

---

## 9. Dépannage

| Problème | Solution |
|----------|----------|
| Page blanche / 500 | Vérifier `storage/logs/laravel.log`. Lancer `php artisan optimize:clear` |
| Image non trouvée | Vérifier `public/images/glasses/...`. Relancer `php artisan glasses:sync` |
| Erreur 404 sur les routes | `sudo a2enmod rewrite` (Apache), ou vérifier la config Nginx |
| Erreur GD non trouvée | `sudo apt install php8.3-gd && sudo systemctl restart php8.3-fpm` |
| Erreur "No application key" | `php artisan key:generate` |
| Assets CSS/JS non chargés | Vérifier APP_URL dans `.env`. Relancer `npm run build` |
