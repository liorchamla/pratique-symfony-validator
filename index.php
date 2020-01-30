<?php

/**
 * TROISIEME PARTIE : CREER SES PROPRES REGLES DE VALIDATION 
 * -----------
 * Vu que les contraintes sont toutes représentées par des classes, on peut évidemment créer nos propres contraintes ! :)
 * 
 * COMMENT SE PRESENTE UNE CONTRAINTE :
 * -----------
 * Une contrainte de validation est représentée par deux classes :
 * - La contrainte elle-même : C'est une classe qui hérite de la classe Constraint et elle porte les options qu'on peut offrir à la contrainte 
 * - Le validateur de la contrainte : C'est une classe qui hérite de ConstraintValidator et c'est elle qui effectue effectivement la validation
 * 
 * Donc, quand le validateur a besoin de valider une valeur avec une contrainte, il appelle le validateur spécifique de la contrainte en lui 
 * passant la valeur et l'instance de la contrainte avec ses options. Exemple :
 * 
 * Imaginons qu'on créé la contrainte suivante :
 * $contrainte = new Email(['message' => 'Adresse invalide']);
 * 
 * Elle n'est là que pour représenter la contrainte et porter ses options (notamment ici le message).
 * Quand on appelle le validateur pour vérifier cette contrainte, il ne fait pas LUI MÊME la validation, il délègue ce travail à la classe
 * EmailValidator (qui accompagne la contrainte Email) en lui passant la valeur ET la contrainte :
 * $emailValidator->validate("lior@gmail.com", $contrainte);
 * 
 * L'objet EmailValidator a alors tous les outils en main pour travailler : la valeur, et la contrainte et ses options
 * 
 * Finalement, le validateur ne fait pas grand chose lui-même, il délègue le travail aux classes de validation et c'est pour ça qu'une
 * contrainte est toujours liée à une classe de validation ! :)
 * 
 * A PRENDRE EN COMPTE QUAND ON CREE UNE CONTRAINTE :
 * ------------
 * La contrainte que l'on créé dans cette section vérifie simplement qu'une valeur est bien une adresse GMAIL en vérifiant qu'elle finit par
 * @gmail.com !
 * 
 * Vous remarquerez qu'elle ne génère pas d'erreur si la valeur qu'on lui passe est vide ! Pourquoi ? Parce qu'une contrainte ne devrait se 
 * soucier QUE et UNIQUEMENT QUE de son domaine (ici le fait que ce soit une adresse GMAIL).
 * 
 * Si on souhaite AUSSI vérifier que la valeur n'est pas vide, pas de soucis, on peut utiliser la contrainte NotBlank !
 * 
 * Ce n'est bien sur qu'un principe que vous pouvez briser si vous savez ce que vous faites.
 * 
 * ------------
 * 
 * Pour bien comprendre ce qu'on a fait durant cette section, voyez les fichiers suivants :
 * - GmailConstraint.php : représente la contrainte et ses options par défaut
 * - GmailConstraintValidator.php : la classe qui valide effectivement qu'une valeur se finit par @gmail.com ... :D
 */


use Symfony\Component\Validator\Validation;

require __DIR__ . '/vendor/autoload.php';

require 'GmailConstraint.php';
require 'GmailConstraintValidator.php';

// Création du validateur
$validator = Validation::createValidator();

// Test avec une valeur invalide
$email = 'lior@hotmail.com';
$resultat = $validator->validate($email, new GmailConstraint());

// lior@hotmail.com: The string "lior@hotmail.com" is not a Gmail address !
var_dump('Test de validation avec une mauvaise adresse : ' . $resultat);

$email = 'lior@gmail.com';
$resultat = $validator->validate($email, new GmailConstraint(['capitals' => true]));

// lior@gmail.com: The string "lior@gmail.com" is not a Gmail address !
var_dump('Test avec l\'option capitals à true et une mauvaise adresse (en minuscules) : ' . $resultat);

// Test avec une valeur valide
$email = 'lior@gmail.com';
$resultat = $validator->validate($email, new GmailConstraint());

var_dump('Test de validation avec adresse valide : ' . $resultat); // "" (aucune erreur)

// Test avec une valeur vide
$email = '';
$resultat = $validator->validate($email, new GmailConstraint());

var_dump('Test avec adresse vide : ' . $resultat); // "" (aucune erreur)
