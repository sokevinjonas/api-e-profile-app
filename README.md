# Naba-Profil

Naba-Profil est une application web open source permettant aux professionnels de créer et partager facilement leur profil via un lien unique ou un code QR. Les utilisateurs peuvent lier leurs réseaux sociaux, ajouter des services qu'ils proposent et personnaliser leur bio. C'est une solution idéale pour remplacer les cartes de visite classiques par un profil en ligne dynamique et accessible.

## Fonctionnalités

-   **Création de profil** : Chaque utilisateur peut créer et personnaliser son profil en ajoutant une bio, un titre de poste et une image de profil.
-   **Liens vers les réseaux sociaux** : Les utilisateurs peuvent ajouter des liens vers leurs comptes de réseaux sociaux (LinkedIn, Twitter, Instagram, etc.).
-   **Services** : Ajout de services proposés avec descriptions et tarifs.
-   **Partage via QR code** : Génération d'un code QR pour partager facilement son profil.
-   **API REST** : Toutes les fonctionnalités sont accessibles via des endpoints API pour une utilisation flexible et extensible.

## Installation

### Prérequis

-   [PHP 8.0+](https://www.php.net/downloads)
-   [Composer](https://getcomposer.org/)
-   [MySQL](https://dev.mysql.com/downloads/)
-   [Node.js](https://nodejs.org/en/download/) et [npm](https://www.npmjs.com/get-npm)

### Étapes

1. **Cloner le dépôt** :
    ```bash
    git clone https://github.com/sokevinjonas/api-Naba-Profil-app.git
    cd api-Naba-Profil-app
    ```
2. **Installer les dépendances PHP** :

    ```bash
    composer install

    ```

3. **Copier le fichier .env.example vers .env et configurer la base de données** :

    ```bash
    cp .env.example .env

    ```

4. **Générer la clé de l'application** :

    ```bash
    cp .env.example .env

    ```

5. **Configurer la base de données dans le fichier .env** :
    ```bash
    DB_CONNECTION=mysql
        DB_HOST=127.0.0.1
        DB_PORT=3306
        DB_DATABASE=e_profile_db
        DB_USERNAME=root
        DB_PASSWORD=
    ```
6. **Exécuter les migrations**:

    ```bash
    php artisan migrate

    ```

## Utilisation

Une fois l'application démarrée, vous pouvez accéder aux endpoints API via http://localhost:8000/api. Vous pouvez vous inscrire, créer un profil, ajouter des services et gérer vos liens sociaux via les endpoints disponibles.

### Exemples d'API :

    GET /api/profile : Récupérer le profil de l'utilisateur connecté.
    POST /api/profile : Créer ou mettre à jour le profil.
    POST /api/profile/services : Ajouter un service.
    POST /api/profile/social-links : Ajouter un lien social.

### Contribution

Naba-Profil est un projet open source, et toutes les contributions sont les bienvenues ! Que ce soit pour corriger un bug, ajouter une fonctionnalité ou améliorer la documentation, n'hésitez pas à faire un pull request.
