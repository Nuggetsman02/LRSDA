# LRS Data Analyst

## Présentation

Le module LRS DataAnalyst est un outil de l'écosystème ULLA v2. Il est dédié à l'extraction, la sélection et l'export des traces d'apprentissage issues du Learning Record Store (LRS) de l'université de Liège. Ce projet s'adresse principalement aux chercheurs et data analysts souhaitant exploiter des données brutes pour des études longitudinales ou statistiques.

## Fonctionnement

Le système ne réalise aucun traitement métier ou calcul complexe ; son rôle est de fournir des ensembles de données filtrés pour une exploitation externe.

- Extraction et Export : Récupération des traces xAPI et export au format CSV.
- Système de Filtres : Sélection par plage de dates, activités spécifiques et verbes xAPI.
- Sémantique et Robustesse : Utilisation de la Connection API pour le formatage des requêtes et du xAPIRegistry pour une sémantique enrichie.

## Stack Technique

- Backend : PHP 8.3 (Slim, Guzzle, simplesamlphp, php-di, psr-7)
- Frontend : HTM/CSS/JS (Bootstrap, jQuery)
- Standard : xAPI

## Déploiement

### 1. Installation des dépendaances

Utilisez Composer pour installer les dépendances :

```bash
composer install
```

### 2. Configuration

Créez un fichier nommé config.json à la racine du projet. Ce fichier doit contenir les identifiants de connexion (credentials) au LRS et aux APIs requises. Voici un exemple de structure pour ce fichier :

```json
{
    "xapi": {
        "uri": "http://LRS/api/xapi",
        "auth_key": "Basic my_auth_key"
    },
    "api_v2": {
        "uri": "http://LRS/api/v2",
        "auth_key": "Basic my_auth_key"
    }
}
```

### 3. Gestion des permissions

L'application doit pouvoir écrire dans certains répertoires pour fonctionner correctement. 
Appliquez les droits d'écriture sur les dossiers suivants :

- Logs de l'application : ```./logs```
- SimpleSAMLphp :
    - ```.simplesammlphp/cache```
    - ```.simplesammlphp/logs```
    - ```.simplesammlphp/data```



#### Ce projet est développé au SMART (Système Méthodologique d'Aide à la Réalisation de Tests) par Dupont Arthur, stagiaire en dernière année d'informatique de gestion.