# t2f
Outil générant un formulaire HTML à partir d'une table SQL. 

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
* Chaque champ de la table devient un champ du formulaire
* Le formulaire s'appelle lui-même (via $_SERVER['PHP_SELF'])
* Les noms des champs du formulaire correspondent aux noms des champs dans la table MySQL
* Chaque champ du formulaire contient un <label> et un <input type="text">
* Si un champ MySQL est auto-incrémenté, il sera "hidden" dans le formulaire HTML
* Si un champ MySQL est NOT NULL, il sera "required" dans le formulaire HTML
* Chaque champ du formulaire est initialisé avec une variable PHP ayant le même nom
* Des boutons <input type="submit"> et  <input type="reset"> sont ajoutés en bas de formulaire

## Sortie
Crée un sous-dossier 'output' (si nécessaire) et y place les formulaires HTML.
Il y a un fichier .php par formulaire 
