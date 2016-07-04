<?php

/**
 * Génère un formulaire à partir du contenu d'une table MySQL
 * Sous Windows, la ligne commande ressemble à ça
 * php.exe -f t2f.php -- -b base -ttable 
 */
/**
 * Vérification des paramètres 
 * -b base de données
 * -t table
 */
$options = getopt("b:t:");  // Récupère les paramètres dans la ligne de commande
//var_dump($options) ; //test
$base = isset($options['b']) ? trim($options['b']) : '';
$table = isset($options['t']) ? trim($options['t']) : '*';
// @todo remplacer ces valeurs en dur
$host = 'localhost';
$user = "root";
$password = "";

/**
 * Connexion à la base de données
 */
try {
  $dsn = 'mysql:host=' . $host . ';dbname=' . $base;
  $dbh = new PDO($dsn, $user, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  e("Impossible de se connecter à la base '" . $base . "'" . PHP_EOL . $e->getMessage());
  exit(99);
}
/**
 * Liste des tables 
 */
if ($table == '*') {
  try {
    // Récupère les tables de la base
    $sql = "show tables";
    $sth = $dbh->query($sql);
    $rows = $sth->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    e("Impossible de lire la liste des tables '" . PHP_EOL . $e->getMessage());
    exit(99);
  }
  $index = 'Tables_in_' . $base;
  foreach ($rows as $row) {
    $tables[] = $row[$index];
  }
} else {
  $tables[] = $table;
}

foreach ($tables as $table) {
  /**
   * Lecture du contenu de la table MySQL demandée
   */
  try {
    // Récupère les colonnes de la table
    $sql = "show full columns from $table";
    $sth = $dbh->query($sql);
    $colonnes = $sth->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    e("Impossible de lire le contenu de la table '" . $table . "'" . PHP_EOL . $e->getMessage());
    exit(99);
  }
  /**
   * Génération du formulaire
   */
  $date = new DateTime();
  $horodatage = $date->format("d/m/Y à H:i:s");
// Entête de formulaire
  $data = '<?php' . PHP_EOL;
  $data .= '/**' . PHP_EOL;
  $data .= ' * Formulaire ' . $table . PHP_EOL;
  $data .= ' *' . PHP_EOL;
  $data .= ' *   Base      : ' . $base . PHP_EOL;
  $data .= ' *   Table     : ' . $table . PHP_EOL;
  $data .= ' *   Généré le : ' . $horodatage . PHP_EOL;
  $data .= ' */' . PHP_EOL;
  $data .= PHP_EOL;
// Récupère le contenu du formulaire
  // $new_string = filter_var($string, FILTER_SANITIZE_STRING);
  foreach ($colonnes as $colonne) {
    /* @var $_POST type */
    $data .= '$' . $colonne['Field'] . ' = isset($_POST["' . $colonne['Field'] . '"]) ? filter_input(INPUT_POST, "' . $colonne['Field'] . '", FILTER_SANITIZE_STRING) : "";' . PHP_EOL;
  }
  $data .= '$submit = isset($_POST["submit"]);' . PHP_EOL;
  $data .= 'if ($submit) {' . PHP_EOL;
  $data .= '   // Formulaire soumis' . PHP_EOL;
  $data .= '} else {' . PHP_EOL;
  $data .= '  // Formulaire non soumis' . PHP_EOL;
  $data .= '}' . PHP_EOL;
  $data.= '?>' . PHP_EOL;
// Formulaire HTML
  $data .= PHP_EOL;
  $data .= '<form id="' . $table . 'Form" action="<?php echo $_SERVER[\'PHP_SELF\']; ?>" method="post">' . PHP_EOL;
  foreach ($colonnes as $colonne) {
    $libelle = trim($colonne['Comment'] != '') ? trim($colonne['Comment']) : str_replace('_', ' ', $colonne['Field']);
    // Si le champ est NOT NULL dans MySQL, on le met à "required" dans le formulaire HTML
    $required = $colonne['Null'] == 'NO' ? ' required ' : '';
    // Si le champ est auto-incrémenté, on le met à "hidden" dans le formulaire HTML
    if (mb_strpos($colonne['Extra'], 'auto_increment') === false) {
      $data .= '  <p><label for="' . $colonne['Field'] . '">' . $libelle . '</label><br/>' . PHP_EOL;
      $data .= '  <input type="text" name="' . $colonne['Field'] . '" id="' . $colonne['Field'] . '" value="<?php echo $' . $colonne['Field'] . '; ?>" ' . $required . '/></p>' . PHP_EOL;
    } else {
      $data .= '  <input type="hidden" name="' . $colonne['Field'] . '" id="' . $colonne['Field'] . '" value="<?php echo $' . $colonne['Field'] . '; ?>" ' . '/>' . PHP_EOL;
    }
  }
  $data .= '  <p><input type="submit" name="submit" value="OK" />&nbsp;<input type="reset" value="Réinitialiser" /></p>' . PHP_EOL;
  $data .= '</form>';

// Vérifie que le dossier de destination existe
  $dirname = 'output';
  if (!file_exists($dirname)) {
    mkdir($dirname, 0777);
  }
// Génère le fichier
  $filename = $table . ".form.php";
  file_put_contents($dirname . DIRECTORY_SEPARATOR . $filename, $data);
}
/**
 * Fonctions communes
 */

/**
 * Affiche un message dans la console Windows
 * @param string $message
 */
function e($message) {
  echo iconv("UTF-8", "CP437//IGNORE", $message);
  //echo iconv("UTF-8", "CP1252//IGNORE", $message);
}
