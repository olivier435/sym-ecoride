# 🚗 EcoRide — README d'installation et d'utilisation

Bienvenue sur le projet **EcoRide**.  
Ce guide décrit l'installation et la prise en main en local.

## 🛠️ Prérequis

- PHP 8.1 ou +
- Composer
- MySQL ou MariaDB (ou compatible avec Doctrine)
- [Symfony CLI (optionnel, conseillé)](https://symfony.com/download)
- Un compte gratuit sur [Mailtrap.io](https://mailtrap.io) (pour les emails de test)

## 0️⃣ Installer Composer (si ce n'est pas déjà fait)

➡️ [Documentation officielle Composer](https://getcomposer.org/download/)

## 1️⃣ Cloner le dépôt Git

```bash
git clone https://github.com/olivier435/sym-ecoride.git
cd sym-ecoride
```

## 2️⃣ Installer les dépendances PHP

```bash
composer install
```

## 3️⃣ Configurer l'environnement
- Copiez .env en .env.local si vous souhaitez surcharger localement.
- Modifiez les variables suivantes dans .env ou .env.local :
```bash
DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/app?serverVersion=8.0.32&charset=utf8mb4"
```
- Configuration locale : app->root, !ChangeMe! -> suppression si aucun mdp exigé, app -> ecoride
- Configurez Mailtrap pour l'envoi d'emails de test :

    - Inscrivez-vous sur Mailtrap.io
    - Allez dans "My Inbox" > "Integration" > "SMTP" > Code Samples : "PHP:Symfony 5+"
    - Copiez la ligne MAILER_DSN et remplacez-la dans .env ou .env.local :

```bash
MAILER_DSN="smtp://xxxxxxxx:****yyyy@sandbox.smtp.mailtrap.io:2525"
```
➡️ Pour remplacer les "xxxxxxxx", vous allez au-dessus dans la partie **Credentials**, puis sur le code à droite de **Username**, vous cliquez pour copier.\
➡️ Pour remplacer les "yyyyyyyy", vous allez au-dessus dans la partie **Credentials**, puis sur le code à droite de **Password**, vous cliquez pour copier.

## 4️⃣ Créer la base de données

```bash
php bin/console doctrine:database:create
```

## 5️⃣ Lancer les migrations

```bash
php bin/console doctrine:migrations:migrate
```

## 6️⃣ Charger les jeux de données (fixtures)

### Tout charger (option recommandée) :

```bash
php bin/console doctrine:fixtures:load --no-interaction
```

### Ou charger par groupe :

```bash
php bin/console doctrine:fixtures:load --group=app --append
php bin/console doctrine:fixtures:load --group=brandModel --append
php bin/console doctrine:fixtures:load --group=car --append
php bin/console doctrine:fixtures:load --group=company --append
php bin/console doctrine:fixtures:load --group=trip --append
```
> L'option --append permet d'ajouter des données sans effacer la base.

## 7️⃣ Démarrer le serveur de développement

```bash
symfony serve
```

### ou

```bash
php bin/console server:run
```

## 8️⃣ Accéder à l'application

Ouvrez votre navigateur sur https://localhost:8000 ou https://127.0.0.1:8000/

## 9️⃣ Comptes de test

| Rôle   | Email                                         | Mot de passe |
| ------ | --------------------------------------------- | ------------ |
| Admin  | [admin@gmail.com](mailto:admin@gmail.com) | password     |
| User 1 | [user0@gmail.com](mailto:user0@gmail.com) | password      |
| Employé | [user1@gmail.com](mailto:user1@gmail.com) | password      |

## 🔁 Commandes utiles

| Description                                            | Commande                                                                                                                                                                                                        |
| ------------------------------------------------------ | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Réinitialiser la base et recharger toutes les fixtures | `php bin/console doctrine:database:drop --force`<br/>`php bin/console doctrine:database:create`<br/>`php bin/console doctrine:migrations:migrate`<br/>`php bin/console doctrine:fixtures:load --no-interaction` |
| Lancer une fixture spécifique        | `php bin/console doctrine:fixtures:load --group=company --append`                                                                                                                                                                          |
| Lancer la commande d'auto-démarrage des trajets        | `php bin/console app:trips:auto-start`                                                                                                                                                                          |
| Lancer la commande d'auto-complétion des trajets       | `php bin/console app:trips:auto-complete`                                                                                                                                                                       |
| Valider automatiquement les passagers                  | `php bin/console app:trip-passengers:auto-validate`                                                                                                                                                             |

## 📦 Déploiement

Pour un déploiement sur Hostinger, consultez la documentation "Déploiement" dans ce dépôt.

## 📝 Documentation

- [Manuel d'utilisation (PDF)](doc/manuel_utilisation_ecoride_v2.pdf)
- [Charte graphique et maquettes (PDF)](doc/charte-graphique-ecoride.pdf)
- [Documentation technique (PDF)](doc/documentation_technique_ecoride.pdf)
- [Gestion de projet (PDF)](doc/ecoride_gestion_projet.pdf)
- [Bonnes pratiques GIT (PDF)](doc/bonnes_pratiques_git_ecoride.pdf)
- [Utilisation de MONGODB (PDF)](doc/process_mongo_db_php_8.3.14.pdf)
- Déploiement

## Base de données

La base de données a été modélisée à l'aide de l'outil jMerise, puis traduite en SQL standard. Elle respecte les bonnes pratiques de normalisation (MCD → MLD → MPD), avec :
- des noms explicites,
- des clés primaires et étrangères nommées,
- des types de données appropriés,
- et des contraintes assurant l'intégrité référentielle.

### Fichiers fournis :
- 🧱 `ecoride_structure.sql` : contient la structure complète (DDL), avec toutes les contraintes (`PRIMARY KEY`, `FOREIGN KEY`, `UNIQUE`, `INDEX`).
- 📦 `ecoride_donnees.sql` : contient un jeu de données cohérent pour simuler l'utilisation de l'application (utilisateurs, trajets, véhicules, préférences, etc.).

### Importation dans MySQL ou PhpMyAdmin :

```bash
mysql -u root -p ecoride < ecoride_structure.sql
mysql -u root -p ecoride < ecoride_donnees.sql
```

## 🆘 Support

Si vous rencontrez un problème à l'installation ou à l'utilisation,\
ouvrez une issue sur le dépôt (https://github.com/olivier435/sym-ecoride/issues) ou contactez le mainteneur.

## Bonne découverte ! 🚗🌱