# Clients LAN de l'ADEPT

Ce workspace Angular contient 3 projets.
1. **user** - L'application de gestion du site web pour les adminisateurs de l'évennement
2. **admin** - L'application pour les utilisateurs standards
3. **core** - La librairie principale, contenant les services et les modèles utilisés par les deux clients et qui communique avec l'API.

# Table des matières
  1. [Information générale](#information-générale)        
  2. [Développer en local](#développer-en-local)
      1. [Outils requis en local](#outils-requis-en-local)  
      2. [Exécuter pour la première fois](#exécuter-pour-la-première-fois)  
      3. [Exécuter](#exécuter)  

## Information générale

 - Version d'Angular: `^7.2.7`
 - Documentation Angular: https://angular.io/docs
 - Documentation de l'API: https://adept-informatique.github.io/lan.adeptinfo.ca/

## Développer en local

 ### Outils requis en local
  - Node.js 8.x ou 10.x (nodejs.org)
  - Angular CLI 

    ```sh
    npm install -g @angular/cli 
    ```
  - (Préférablement) une instance fonctionnelle de l'API du Lan de l'ADEPT en local

### Exécuter pour la première fois

 - Avec un terminal de commande, se placer à la racine du dossier **core** : `cd clients/projects`
 - Exécuter `npm install` (prend un certain temps)
 - Exécuter `ng build core` pour compiler la librairie principale
 - Retourner à la racine du dossier **clients** `cd ..` :
 - Exécuter un second `npm install` (prend un certain temps)
 - Remplacer le fichier `projects/admin/src/environments/environment.example.ts` par `environment.example.ts` et le fichier `projects/core/src/lib/params.example.ts` par `params.ts`
    - Veuillez contacter un administrateur du projet pour avoir les configurations d'environnement préremplie.

 > :bulb: IMPORTANT: Il faut faire un `ng build core` à chaque modification de la librairie pour que les changements soient pris en compte dans les clients.

 - Lancer la commande `ng serve <nom du projet>` en remplaçant `<nom du projet>` par `admin` ou `user` pour démmarer l'application de votre choix.

 - Ouvrir un navigateur à l'URL suivante: [http://localhost:4200](http://localhost:4200)

### Exécuter
- Exécuter `ng build core` pour compiler la librairie core
- Lancer la commande `ng serve <nom du projet>` en remplaçant `<nom du projet>` par `admin` ou `user` pour démmarer l'application de votre choix

- Ouvrir un navigateur à l'URL suivante: [http://localhost:4200](http://localhost:4200)
