<?php

/**
 * BIENVENUE DANS CE COURS SUR LE COMPOSANT SYMFONY/VALIDATOR :
 * ---------------
 * Vous le savez, à chaque instant dans une application web, nous avons à faire à des données envoyées par les utilisateurs. Et il est connu
 * que la première source d'erreur, ce sont justement les données envoyées par les utilisateurs !
 * 
 * Il est donc important que l'on puisse valider des données dans nos applications, et le composant symfony/validator est la librairie parfaite
 * pour ce faire. Il en existe des dizaines que vous pourrez découvrir sur packagist.org mais nous nous concentrerons ici sur le validateur de
 * Symfony car c'est celui que l'on retrouve dans de nombreux projets et bien sur dans le framework lui-même.
 * 
 * DECOUVERTE DU COMPOSANT :
 * ------------
 * L'installation du composant (composer require symfony/validator) vous donne accès à deux choses :
 * - Une liste de contraintes (règles, obligations) que vous pouvez utiliser pour valider des données
 * - Un validateur dont la mission est de valider une donnée par rapport à une ou plusieurs contraintes
 * 
 * Chaque contrainte est matérialisée sous la forme de 2 classes, et vous pouvez créer vos propres contraintes, donc vos propres règles de
 * validation.
 * 
 */

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validation;

require __DIR__ . '/vendor/autoload.php';

/**
 * COMMENT OBTENIR LE VALIDATEUR ?
 * ----------
 * Pour obtenir le validateur (dont le but est de prendre une donnée, et de s'assurer qu'elle respecte bien la ou les règles voulues), on peut
 * utiliser deux méthodes :
 * - Validation::createValidator() va nous renvoyer un validateur par défaut, avec des configurations pré-définies
 * - Validation::createValidatorBuilder() va nous renvoyer un constructeur de validateur pour qu'on puisse nous même le configurer !
 * 
 * Utilisons ici le validateur par défaut :
 */
$validator = Validation::createValidator();

/**
 * DECOUVERTE DES CONTRAINTES DE VALIDATION :
 * -----------
 * Le validateur est créé et il permet de vérifier si une donnée respecte bien certaines contraintes. Mais comment représenter les contraintes
 * que la donnée doit respectée ?
 * 
 * Faites connaissances avec les Constraints ! Ce sont des objets qui contiennent des règles spécifiques et représentent donc des .. contraintes !
 * 
 * Vous en avez plusieurs sortes :
 * - Des contraintes de base (non vide, non nulle, vraie, fausse, etc)
 * - Des contraintes sur les chaines de caractères (doit respecter telle longueur, doit être un email, doit être une URL, doit respecter
 * telle expression réulière, etc)
 * - Des contraintes numériques (doit être positif, doit être négatif, etc)
 * - Des contraintes de comparaison (doit être plus grand que, plus petit que, etc)
 * - voir toutes les contraintes ici : https://symfony.com/doc/current/validation.html#constraints
 * 
 * Encore une fois, ces contraintes sont représentée par des classes.
 * 
 * Faisons plusieurs tests et quelques var_dump pour voir ce que ça donne :
 */
$email = 'valeur-bidon';

/**
 * APPELER LE VALIDATEUR :
 * -----
 * Pour appeler le validateur, on appelle la méthode validate en lui passant :
 * 1) La donnée à valider
 * 2) La contrainte à utiliser (une instance de la classe qui représente la contrainte)
 * 
 * Ici nous prenons l'exemple de la contrainte Email.
 */
$resultat = $validator->validate($email, new Email());
var_dump('Validation simple : ' . (string) $resultat); // valeur-bidon: This value is not a valid email address. (code bd79c0ab-ddba-46cc-a703-a7a4b08de310)

/**
 * UTILISER DES OPTIONS SUR LES CONTRAINTES 
 * -----------
 * Les contraintes peuvent prendre en compte des options qu'on passe sous la forme d'un tableau associatif lors de l'instanciation de la 
 * contrainte !
 * 
 * Chaque contrainte a des options spécifiques, et certaines options sont communes à toutes les contraintes.
 * 
 * Ici nous souhaitons personnaliser le message d'errueur de la contrainte Email en cas d'invalidité :
 */
$resultat = $validator->validate($email, new Email(['message' => 'L\'email n\'est pas valide !']));
var_dump('Validation avec option (message) : ' . (string) $resultat); // valeur-bidon: L'email n'est pas valide ! (code bd79c0ab-ddba-46cc-a703-a7a4b08de310)

// Bien sûr, on n'aura aucun message d'erreur si la donnée est conforme ;)
$email = 'lior@gmail.com';
$resultat = $validator->validate($email, new Email());
var_dump('Validation correcte : ' . (string) $resultat); // ""

/**
 * APPLIQUER PLUSIEURS CONTRAINTES SUR UNE DONNEE :
 * -----------
 * On peut aussi demander à appliquer plusieurs contraintes. La donnée devra donc passer tous les tests pour être considérée comme valide.
 * 
 * Ici, on soumet notre $email à :
 * - NotBlank : la valeur ne doit pas être vide
 * - Email : elle doit être un email valide
 * 
 * Si une contrainte ne passe pas, le validateur n'ira pas plus loin et ne nous remontera que l'erreur en question. On s'attend donc ici à 
 * ce qu'il s'arrête à la contrainte NotBlank
 */
$email = '';
$resultat = $validator->validate($email, [
    new NotBlank(),
    new Email()
]);
var_dump('Validation multiple : ' . (string)  $resultat); // This value should not be blank. (code c1051bb4-d103-4f74-8988-acbcafc7fdc3)

/**
 * COMPRENDRE CE QUE NOUS RETOURNE LE METHODE VALIDATE()
 * ----------
 * Lorsque l'on appelle la méthode validate(), ce qu'elle nous retourne est un objet de la classe ConstraintViolationList. Cette classe est
 * particulièrement utile pour gérer, traiter, analyser le résultat de la validation.
 * 
 * Voyons ce qu'elle sait faire :
 */

// 1) On peut utiliser le $resultat comme une simple chaine de caractères
var_dump((string) $resultat);

// 2) On peut compter les erreurs qui ont eu lieu
var_dump($resultat->count()); // 1

// 3) On peut boucler sur les erreurs si il y en a plusieurs
foreach ($resultat as $error) {
    // Ici, $error est une instance de la classe ConstraintViolation
    $error->getMessage(); // Donne le message d'erreur
    $error->getConstraint(); // Donne la contrainte qui a généré cette erreur
}

/**
 * D'autres méthodes plus poussées existent sur le résultat :
 * - $resultat->add(...) : permet d'ajouter une erreur au résultat
 * - $resultat->set(...) : permet de modifier une erreur existante dans le résultat
 * - $resultat->remove(...) : permet de supprimer une erreur du résultat
 */
