<?php

use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * DECOUVREZ LA CLASSE USER
 * ---------
 * C'est une classe d'exemple qui représente un objet simple avec un prénom, un nom et un âge.
 * 
 * La partie intéressante est la méthode statique que j'ai ajouté : loadValidatorMetadata().
 * 
 * Nous allons configurer le validateur pour lui expliquer que, si on lui demande de valider un objet, il devra TOUT D'ABORD appeler cette
 * méthode pour obtenir la configuration des contraintes qui pèsent sur les propriétés de l'objet
 */
class User
{
    public $firstName;
    public $lastName;
    public $age;

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        /**
         * La variable $metdata représente la configuration (les données supplémentaires qu'on précise au validateur) des contraintes. Cet
         * objet possède plusieurs méthodes intéressantes dont 2 qui sont cruciales :
         * - addPropertyConstraint(nomDeLaPropriété, contrainte) permet d'ajouter une contrainte sur une propriété
         * - addPropertyConstraints(nomDeLaPropriété, tableauDeContraintes) permet d'ajouter plusieurs contraintes sur une propriété
         * 
         * Une fois que notre méthode aura fini le boulot, le validateur pourra intéroger cet objet $metadata pour savoir quelles sont les
         * contraintes qui pèsent sur quelle propriété :)
         */
        $metadata
            ->addPropertyConstraints('firstName', [
                new NotBlank(),
                new Length(['min' => 3])
            ]);
        // ->addPropertyConstraints('lastName', [
        //     new NotBlank(),
        //     new Length(['min' => 3])
        // ])
        // ->addPropertyConstraint('age', new GreaterThanOrEqual(['value' => 10]));
    }
}
