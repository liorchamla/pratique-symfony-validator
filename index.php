<?php

/**
 * REFACTORING FINAL :
 * ---------
 * 1) config/bootstrap.php : On a bougé tout le code de configuration du validateur etc 
 * 2) src/Model/User.php : On a ajouté une contrainte GmailConstraint sur une propriété email
 * 3) src/Constraint/GmailConstraint.php : On a ajouté l'annotation @Annotation sur la classe pour qu'on puisse s'en servir comme d'une 
 * annotation
 * 4) composer.json : On a ajouté une politique d'autoloading pour gérer notre nouvelle architecture
 */

use App\Model\User;

require __DIR__ . '/config/bootstrap.php';

$user = new User();
$user->firstName = '';
$user->lastName = '';
$user->age = 2;
$user->email = 'lior@hotmail.com';

// Test de validation sans rien préciser
$resultat = $validator->validate($user);
var_dump("Sans préciser de groupe : " . $resultat->count()); // donne 2, car l'âge est faux et l'adresse n'est pas une gmail

// Test avec le groupe "identity"
$resultat = $validator->validate($user, null, "identity");
var_dump("En précisant le groupe identity : " . $resultat->count()); // Donne 4, car seules les 4 contraintes de firstName et lastName foirent

// Test avec les deux groupes (Default, qui veut dire toutes les contraintes "sans groupe", puis "identity", qui veut dire toutes les
// contraintes du groupe "identity)
$resultat = $validator->validate($user, null, ['Default', 'identity']);
var_dump("En précisant les groupes Default et identity : " . $resultat->count()); // Donne 6 : on a tout analysé et tout est foireux.
