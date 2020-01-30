<?php

/**
 * QUATRIEME PARTIE (TIERS) : VALIDER DES OBJETS ? AVEC UNE CONFIG EN ANNOTATIONS !
 * ------------
 * Dans la section précédente, on commençait à voir comment valider un objet simplement via une méthode statique à l'intérieure de la classe
 * qui explique au validateur les contraintes à valider.
 * 
 * On peut enfin pour cela passer par les Annotations directement au sein de la classe elle-même. Les annotations sont des indications
 * données dans les blocs de commentaires au dessus d'une classe, d'une propriété ou d'une méthode. Elle ne sont pas gérées nativement en PHP
 * mais peuvent l'être si on installe les packages adéquats (composer require doctrine/annotations doctrine/cache).
 * 
 * Les avantages sont multiples :
 * 1) Elles sont simples à écrire et à apprendre
 * 2) La configuration des contraintes et le code de la classe sont dans le même fichier
 * 3) Elles sont courtes à écrire :D
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
 * - config/validation.yml : on a commenté l'ensemble de la configuration pour laisser place aux annotations
 * - User.php : la classe que l'on souhaite valider (où nous avons commenté le contenu de la méthode statique qui est maintenant dans les 
 * annotations)
 * - index.php : on utilise le validatorBuilder pour configurer le validateur
 */

use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\Validator\Validation;

require __DIR__ . '/vendor/autoload.php';

require 'User.php';
require 'GmailConstraint.php';
require 'GmailConstraintValidator.php';

/**
 * CONFIGURER DOCTRINE ANNOTATIONS :
 * -------
 * Les annotations présentes dans notre classe sont elles mêmes des classes, et quand Doctrine va LIRE les annotation, il va vouloir
 * instancier ces classes. Le problème est que Doctrine ne se sert pas de notre fichier d'autoload.php tant qu'on ne le lui dit pas.
 * 
 * Ici nous lui donnons donc le moyen de retrouver les classes représentées dans nos annotations.
 */
$loader = require __DIR__ . '/vendor/autoload.php';
AnnotationRegistry::registerLoader([$loader, 'loadClass']);


// Création du validateur
$validator = Validation::createValidatorBuilder()
    // On indique au Builder que le validateur qu'il va nous donner doit être capable de trouver les contraintes de validation sur un objet
    // en appelant une méthode static qui s'appelle loadValidatorMetadata (bien sur on aurait pu choisir un autre nom mais c'est une
    // convention)
    ->addMethodMapping('loadValidatorMetadata')
    // On indique au Builder que le validateur doit aussi être capable d'aller lire des configuration d'objets directement dans un fichier 
    // de configuration YAML
    ->addYamlMapping(__DIR__ . '/config/validation.yml')
    // On indique qu'on souhaite aussi être capable d'extraire les contraintes qui pèsent sur les propriétés d'un objet via les annotations
    // qui se trouvent au dessus des propriétés / méthodes
    ->enableAnnotationMapping()
    // Notre validateur est prêt, on demande au builder de nous l'offrir
    ->getValidator();

// Créons un User avec des données invalides (pour voir les contraintes qui pèsent sur les propriétés, allez voir le fichier User.php et sa 
// méthode statique loadValidatorMetadata)
$user = new User();
$user->firstName = '';
$user->lastName = '';
$user->age = 2;

$resultat = $validator->validate($user);
var_dump($resultat);
