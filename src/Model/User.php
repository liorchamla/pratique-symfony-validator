<?php

namespace App\Model;

use App\Constraint\GmailConstraint;
use Symfony\Component\Validator\Mapping\ClassMetadata;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * DECOUVREZ LA CLASSE USER
 * ---------
 * C'est une classe d'exemple qui représente un objet simple avec un prénom, un nom et un âge.
 * 
 * Regardez bien les commentaires au dessus des propriétés ! Ce sont les annotations qui permettent d'exprimer pour chaque propriété
 * les contraintes de validation qui pèse dessus !
 * 
 * Nous allons configurer le validateur pour lui expliquer que, si on lui demande de valider un objet, il devra se servir de ces annotations 
 * pour en déduire les contraintes à appliquer !
 * 
 * IMPORTANT :
 * ----------
 * Notez que je n'utilise plus uniquement le nom des contraintes (NotBlank, Email) mais un espace de nom Assert (voir en haut du fichier)
 * ce qui me permet de ne pas avoir à ajouter tous les "use" pour chaque classe utilisée.
 */
class User
{
    /**
     * @Assert\NotBlank(groups="identity")
     * @Assert\Length(min=3, groups="identity")
     */
    public $firstName;

    /**
     * @Assert\NotBlank(groups="identity")
     * @Assert\Length(min=3, groups="identity")
     */
    public $lastName;

    /**
     * @GmailConstraint()
     */
    public $email;

    /**
     * @Assert\GreaterThanOrEqual(value=18)
     */
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
        // $metadata
        //     ->addPropertyConstraints('firstName', [
        //         new NotBlank(),
        //         new Length(['min' => 3])
        //     ]);
        // ->addPropertyConstraints('lastName', [
        //     new NotBlank(),
        //     new Length(['min' => 3])
        // ])
        // ->addPropertyConstraint('age', new GreaterThanOrEqual(['value' => 10]));
    }
}
