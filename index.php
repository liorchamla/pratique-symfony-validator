<?php

/**
 * QUATRIEME PARTIE : VALIDER DES OBJETS ? OUI ! ON PEUT
 * ------------
 * Si vous avez déjà utilisé le framework Symfony, vous avez vu qu'il nous arrive très souvent de valider non pas une simple valeur, non pas
 * un tableau associatif, mais bel et bien des objets (entités ou DTO) ! Et oui, le composant est bel et bien capable d'examiner un objet
 * afin de voir si ses propriétés respectent bien telles ou telles contraintes !
 * 
 * UNE MISE EN PLACE A FAIRE :
 * ------------
 * Jusqu'ici, nous pouvions nous suffire du composant lui même pour travailler, mais si l'on veut valider des objets, il faudra indiquer 
 * au validateur quelles sont les contraintes qui pèsent sur ses propriétés.
 * 
 * On appelle ça les METADATAS : ce sont des informations SUPPLEMENTAIRES à propos des propriétés de l'objet. Dans notre cas, on parle parfois
 * de VALIDATOR METADATAS, puisqu'elles sont destinées au Validateur
 * 
 *  et pour ce faire, il faudra décrire ces contraintes dans 
 * une configuration quelconque :
 * - Directement dans la classe de l'objet en ajoutant une méthode statique loadValidatorMetadata que le validateur appellera pour connaitre
 * les contraintes qui pèsent sur les propriétés
 * - Directement dans la classe encore, en utilisant des annotations qu'on posera sur les propriétés elles-mêmes (nécessite les packages
 * doctrine/annotations et doctrine/cache)
 * - Dans un fichier de configuration YAML (nécessite le composant symfony/yaml)
 * 
 * CONFIGURER LE VALIDATEUR :
 * ----------
 * Par conséquence, le validateur par défaut est trop simple, il ne tient absolument pas compte de tout ce que je viens de décrire et il nous
 * faut donc un validateur plus intelligent !
 * 
 * Il existe 2 façon d'obtenir un validateur :
 * - Validation::createValidator() nous renvoie le validateur par défaut, efficace mais incapable de prendre en compte des configurations 
 * spéciales
 * - Validation::createValidatorBuilder() nous renvoie un constructeur de validateur et nous permet donc de configurer le futur validateur
 * que l'on recevra au final !
 * 
 * Nous allons donc passer par le ValidatorBuilder pour configurer notre validateur afin de prendre en compte ces nouvelles configurations
 * 
 * ------------
 * 
 * Pour bien comprendre ce qu'on a fait durant cette section, voyez les fichiers suivants :
 * - User.php : la classe que l'on souhaite valider
 * - index.php : on utilise le validatorBuilder pour configurer le validateur
 */


use Symfony\Component\Validator\Validation;

require __DIR__ . '/vendor/autoload.php';

require 'User.php';
require 'GmailConstraint.php';
require 'GmailConstraintValidator.php';

// Création du validateur
$validator = Validation::createValidatorBuilder()
    // On indique au Builder que le validateur qu'il va nous donner doit être capable de trouver les contraintes de validation sur un objet
    // en appelant une méthode static qui s'appelle loadValidatorMetadata (bien sur on aurait pu choisir un autre nom mais c'est une
    // convention)
    ->addMethodMapping('loadValidatorMetadata')
    // Notre validateur est prêt, on demande au builder de nous l'offrir
    ->getValidator();

// Créons un User avec des données invalides (pour voir les contraintes qui pèsent sur les propriétés, allez voir le fichier User.php et sa 
// méthode statique loadValidatorMetadata)
$user = new User();
$user->firstName = '';
$user->lastName = '';
$user->age = 2;

/**
 * REMARQUE IMPORTANTE :
 * ---------
 * Vous vous rappelez des précédentes sections ? A chaque fois qu'on appelait la méthode validate(), on lui donnait 2 paramètres :
 * - La valeur à valider (valeur simple ou tableau complexe, peu importe)
 * - Les contraintes à appliquer
 * 
 * Ici, remarquez qu'on ne donne que l'objet que l'on souhaite valider. Dans ce cadre, le validateur sait qu'il doit tirer la configuration
 * des contraintes d'ailleurs et va donc appeler la méthode statique que l'on a précisé lors de la construction sur l'objet en question.
 * 
 * Magique.
 */
$resultat = $validator->validate($user);
var_dump($resultat);
