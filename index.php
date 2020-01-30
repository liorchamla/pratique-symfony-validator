<?php

/**
 * QUATRIEME PARTIE (BIS) : VALIDER DES OBJETS ? AVEC UNE CONFIG EN YAML !
 * ------------
 * Dans la section précédente, on commençait à voir comment valider un objet simplement via une méthode statique à l'intérieure de la classe
 * qui explique au validateur les contraintes à valider.
 * 
 * Il est aussi possible de passer par un fichier de configuration YAML ! Pour ça, on a besoin du composant symfony/yaml (composer require
 * symfony/yaml) qui permet de lire les fichiers YAML et d'en déduire un tableau.
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
 * - config/validation.yml : le fichier de configuration du validateur au format YAML
 * - User.php : la classe que l'on souhaite valider (où nous avons commenté le contenu de la méthode statique qui est maintenant dans le
 * fichier YAML)
 * - index.php : on utilise le validatorBuilder pour configurer le validateur
 */

use Symfony\Component\Validator\Validation;

require __DIR__ . '/vendor/autoload.php';

require 'User.php';
require 'GmailConstraint.php';
require 'GmailConstraintValidator.php';

/**
 * REMARQUE IMPORTANTE :
 * --------
 * A ce stade, nous avons déjà vu comment créer les métadatas du validateur au sein même de la classe (via l'appel d'une méthode statique) et
 * nous ajoutons maintenant la configuration YAML. Mais alors, que se passe-t-il si on a un conflit entre les deux ?
 * 
 * Notez bien que SELON L'ORDRE DANS LEQUEL VOUS CONSTRUISEZ LE VALIDATEUR (voir ci-dessous), le YAML écrasera ce que dit la classe ou alors
 * la classe écrasera ce que dit le YAML.
 * 
 * Par exemple : dans le fichier config/validation.yml je dis que le firstName doit faire minimum 5 caractères alors que dans la classe je dis
 * qu'il lui fait 3 caractères minimum.
 * 
 * Vous constaterez que c'est la contrainte du YAML qui s'applique mais si vous inversez les deux méthodes ci-dessous lors de la construction 
 * du validateur, vous verrez que c'est la classe qui prend la main.
 * 
 * En gros : c'est le dernier qui parle qui a raison :)
 */
// Création du validateur
$validator = Validation::createValidatorBuilder()
    // On indique au Builder que le validateur qu'il va nous donner doit être capable de trouver les contraintes de validation sur un objet
    // en appelant une méthode static qui s'appelle loadValidatorMetadata (bien sur on aurait pu choisir un autre nom mais c'est une
    // convention)
    ->addMethodMapping('loadValidatorMetadata')
    // On indique au Builder que le validateur doit aussi être capable d'aller lire des configuration d'objets directement dans un fichier 
    // de configuration YAML
    ->addYamlMapping(__DIR__ . '/config/validation.yml')
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
 * des contraintes d'ailleurs et va donc lire le fichier YAML que l'on a précisé lors de la construction sur l'objet en question.
 * 
 * Magique.
 */
$resultat = $validator->validate($user);
var_dump($resultat);
