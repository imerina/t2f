# t2f
Outil générant un formulaire HHTML à partir d'une table SQL. 

Fonctionne en ligne de commande via PHP.

## Paramètres

* -b la base de données MySQL
* -t la table (* si toutes les tables de la base doivent être générées)
* Les user/password sont écrits en dur dans le script PHP de l'outil

## Utilisation

php.exe -f t2f.php -- -b MaBase

php.exe -f t2f.php -- -b MaBase -t MaTable

## Fonctionnalités
* Chaque table devient un formulaire HTML/PHP
* Chaque champ devient un champ de ce formulaire
* Le formulaire s'appelle lui-même (via $_SERVER['PHP_SELF'])
* Le nom des champs du formulaire correspond aux noms des champs dans la table MySQL
* Chaque champ du formulaire contient un <label> et un <input type="text">
* Chaque champ du formulaire peut être initialisé avec une variable PHP
* Un bouton <input type="submit"> est ajouté en bas de formulaire

## Sortie
Crée un sous-dossier 'output' (si nécessaire) et y place les formulaires HTML.
Il y a un fichier .php par formulaire 
