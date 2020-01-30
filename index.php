<?php

/**
 * DEUXIEME PARTIE : COMMENT VALIDER PLUSIEURS DONNEES EN MÊME TEMPS
 * --------------
 * Nous avons vu comment nous pouvions valider une donnée (un email) mais la plupart du temps, ce que l'on voudra valider c'est un
 * ensemble de données.
 * 
 * On peut donc tout à fait valider un tableau de données !
 * 
 * LES CONTRAINTES DE SURENSEMBLE :
 * ----------
 * Il existe des contraintes dont le but est de contenir d'autres contraintes et de les organiser :
 * - Collection : elle permet de créer une collection de contraintes qui correspondent aux données qu'on veut valider
 * - All : elle permet de créer une ou plusieurs contraintes qu'il faudra que toutes les données d'un tableau respecte
 * 
 */

use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validation;

require __DIR__ . '/vendor/autoload.php';

// Création du validateur
$validator = Validation::createValidator();

/**
 * IMAGINONS UN TABLEAU DE DONNEES CLASSIQUES :
 * ----------
 * Prenons le cas d'un formulaire d'inscription à notre site par exemple
 */
$inscription = [
    'firstName' => 'Lior',
    'lastName' => 'Chamla',
    'email' => 'lior@gmail.com',
    'password' => '123456789',
    'age' => 33
];

/**
 * DECOUVERTE DE LA CONTRAINTE DE COLLECTION :
 * ------------
 * La contrainte Collection nous permet de décrire une collection de contraintes appliquées à des champs de notre tableau associatif.
 * 
 * Elle va nous permettre de valider le tableau ci-dessus ! Vous remarquerez d'ailleurs que la structure du tableau de contraintes que nous
 * créons correspond exactement à celle du tableau de données !
 */
$collection = new Collection([
    // Les contraintes sur la clé firstName
    'firstName' => [
        new NotBlank(),
        new Length(['min' => 3, 'max' => 50])
    ],
    // Les contraintes sur la clé lastName
    'lastName' =>  [
        new NotBlank(),
        new Length(['min' => 3, 'max' => 50])
    ],
    // Les contraintes sur la clé email
    'email' => [
        new NotBlank(),
        new Email()
    ],
    // Les contraintes sur la clé password
    'password' => [
        new NotBlank(),
        new Length(['min' => 8])
    ],
    // Les contraintes sur la clé age
    'age' => new GreaterThanOrEqual(['value' => 18]) // marche aussi avec GreaterThan(['value' => 17]) ;))
]);

$resultat = $validator->validate($inscription, $collection);
var_dump('Validation de tableau associatif plat :', $resultat);

/**
 * CA MARCHE AUSSI AVEC UN TABLEAU INDEXE (NON ASSOCIATIF)
 */
$inscription = [
    'Lior',
    'Chamla',
    33
];
$collection = new Collection([
    0 => new NotBlank(),
    1 => new NotBlank(),
    2 => new GreaterThanOrEqual(['value' => 18])
]);

$resultat = $validator->validate($inscription, $collection);
var_dump('Validation de tableau indexé : ', $resultat);


/**
 * VALIDATION DE STRUCTURES PLUS COMPLEXES :
 * ------------
 * Jusque là, on est déjà bien impressioné par la puissance du Validateur, mais on peut faire encore plus !
 * 
 * On peut créer une collection de contraintes sur un tableau multidimensionnel !
 */
$inscription = [
    'age' => 33,
    'name' => [
        'first' => 'Lior',
        'last' => 'Chamla'
    ],
    'coords' => [
        'email' => 'lior@gmail.com',
        'city' => 'Marseille'
    ]
];

// Notez encore une fois que la structure de la Collection est la même que celle du tableau de base !
$collection = new Collection([
    'age' => new GreaterThanOrEqual(['value' => 18]),
    'name' => new Collection([
        'first' => new NotBlank(),
        'last' => new NotBlank()
    ]),
    'coords' => new Collection([
        'email' => [
            new NotBlank(),
            new Email()
        ],
        'city' => [
            new NotBlank(),
            new Choice(['choices' => ['Marseille', 'Paris', 'Tokyo']])
        ]
    ])
]);

$resultat = $validator->validate($inscription, $collection);
var_dump('Validation de tableaux imbriqués : ', $resultat);

/**
 * BONUS DE STYLE : LA CONTRAINTE ALL
 * ---------
 * La contrainte All vous permet de valider un tableau en prenant soin de vérifier que CHAQUE valeur correspond à des règles
 */
$agesEtudiants = [19, 23, 20, 33, 7, 22, 85];
// Assurons nous que chaque étudiant du tableau a plus de 18 ans
$contrainte = new All(['constraints' => [
    new GreaterThanOrEqual(18),
    new LessThanOrEqual(70)
]]);

// La validation va repérer 2 erreurs : la personne qui a 7 ans et la personne qui a 85 ans !
$resultat = $validator->validate($agesEtudiants, $contrainte);
var_dump('Validation des données d\'un tableau : ', $resultat);
