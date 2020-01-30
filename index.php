<?php

/**
 * DERNIERE PARTIE : VALIDER UNIQUEMENT CERTAINES DONNEES (LES GROUPES DE VALIDATION)
 * ------------
 * Nous avons fait le tour des questions basiques à propos du composant de validation de Symfony mais il reste une notion très utile à 
 * appréhender : les groupes de validation.
 * 
 * Ils permettent de ne valider qu'une partie des champs et non pas tous.
 * 
 * COMMENT CELA FONCTIONNE :
 * ------------
 * Comme vous le savez, les contraintes peuvent prendre en paramètre des options qui leur sont spécifiques par exemple :
 * - La contrainte Length qui a une option "min", une option "max" et des options "minMessage" et "maxMessage"
 * - La contrainte NotBlank qui a une option "message"
 * - etc
 * 
 * Néanmoins il y a une option que toutes les contraintes possèdent : groups 
 * 
 * Elle permet d'exprimer qu'une contrainte fait partie d'un ou plusieurs groupes de validation. Et cela nous permet lorsque l'on valide un
 * objet, de valider toutes les propriétés porteuses de contraintes, ou uniquement celles qui font partie de tel ou tel groupe !
 * 
 * EXEMPLE :
 * ------------
 * Prenons notre classe User : imaginons que parfois, l'âge n'ait pas d'importance, et que parfois, l'âge ait de l'importance. Donc on veut
 * parfois valider uniquement l'identité de l'user, et parfois, uniquement l'âge et même parfois le tout !
 * 
 * Il va donc s'agir de mettre les contraintes qui pèsent sur le firstName et le lastName dans un groupe spécial (le nom importe peu mais 
 * nous avons choisi "identity") et de n'assigner la contrainte qui pèse sur l'age à un autre groupe voire même à aucun groupe 
 * particulier (c'est le choix que nous avons fait ici).
 * 
 * Ainsi, quand on ne veut valider que l'identité, on peut demander au validateur de n'étudier que les contraintes dont le groupe est
 * "identity" (et qui pèse donc sur le prénom et le nom)
 * 
 * Si au contraire on ne veut valider que la contrainte qui pèse sur l'âge (GreaterThanOrEqual), on appelle le validateur sans préciser de
 * groupe de validation.
 * 
 * ATTENTION :
 * ------------
 * Ne pensez pas que ce qui compte pour les groupes de validation ce sont les propriétés, ce n'est pas le cas, ce qu'on inscrit dans un groupe
 * c'est une contrainte qui pèse sur une propriété.
 * 
 * En d'autres termes, je peux très bien imaginer un cas où je veux uniquement valider le NotBlank sur le firstName, et pas le Length.
 * 
 * LE GROUPE CONCERNE LES CONTRAINTES, PAS LES PROPRIETES !
 * 
 * GROUPE DE VALIDATION PAR DEFAUT :
 * -------------
 * Vous l'aurez compris :
 * - si j'appelle le validateur en précisant le groupe "identity", seules les contraintes qui font partie de ce groupe seront étudiées
 * - si j'appelle le validateur en ne précisant rien, seules contraintes qui n'ont pas de groupe seront étudiées
 * 
 * Mais alors comment faire si on veut tout valider, et pas seulement l'identité ou seulement l'âge. AUCUN SOUCIS :D
 * 
 * On considère que toutes les contraintes qui n'ont pas de groupe assigné sont en fait malgré tout assignées au groupe appelé "Default"
 * 
 * Et le validateur nous permet tout à fait d'étudier plusieurs groupes à la fois. Donc :
 * - si j'appelle le validateur en précisant les deux groupes "Default" et "identity", il tiendra compte de toutes les contraintes sans groupe
 * et de toutes les contraintes du groupe "identity" (autrement dit, il va tout valider)
 * 
 * ------------
 * 
 * Pour bien comprendre ce qu'on a fait durant cette section, voyez les fichiers suivants :
 * - User.php : assignation des contraintes à des groupes différents ("identity" pour les contraintes du firstName et lastName, aucun groupe
 * particulier pour la contrainte sur l'âge)
 * - index.php : différents tests avec différents groupes
 * - config/validation.yml : on a aussi ajouté la notion de groups juste pour montrer la syntaxe à utiliser en YAML
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

// Test de validation sans rien préciser
$resultat = $validator->validate($user);
var_dump("Sans préciser de groupe : " . $resultat->count()); // donne 1, car seule la contrainte sur l'âge a été étudiée et a causé une erreur

// Test avec le groupe "identity"
$resultat = $validator->validate($user, null, "identity");
var_dump("En précisant le groupe identity : " . $resultat->count()); // Donne 4, car seules les 4 contraintes de firstName et lastName foirent

// Test avec les deux groupes (Default, qui veut dire toutes les contraintes "sans groupe", puis "identity", qui veut dire toutes les
// contraintes du groupe "identity)
$resultat = $validator->validate($user, null, ['Default', 'identity']);
var_dump("En précisant les groupes Default et identity : " . $resultat->count()); // Donne 5 : on a tout analysé et tout est foireux.
