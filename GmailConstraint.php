<?php

use Symfony\Component\Validator\Constraint;

/**
 * CREEONS NOTRE PROPRE CONTRAINTE : LA GMAILCONSTRAINT !
 * ------------
 * Notre but est de créer une contrainte qui permettra d'être sur qu'une adresse est une adresse GMAIL.
 * 
 * On veut donc une contrainte qui puisse prendre deux options :
 * - message : le message d'erreur à afficher si la valeur est invalide
 * - capitals : le fait qu'on veuille absolument que la valeur soit en MAJUSCULES ou pas
 * 
 * Et oui, cette classe ne contient AUCUNE LOGIQUE DE VALIDATION, celle-ci sera effectuée par une deuxième classe : GmailConstraintValidator
 */
class GmailConstraint extends Constraint
{
    /**
     * On définit un message par défaut
     * Vous remarquez qu'on peut utiliser des variables (appelées "parameters") dans le message d'erreur. C'est la classe de validation
     * qui "bouchera les trous" lors de la validation
     */
    public $message = 'The string "{{ value }}" is not a Gmail address !';

    /**
     * On définit l'option capitals par défaut à faux (on s'en fout que ce soit en majuscules ou pas)
     * 
     * Notez que c'est complètement inventé, ce n'est qu'un exemple, j'aurai pu appeler cette option "cocoLasticot" si je voulais
     * Le tout est que la classe de validation comprenne à quoi ça sert !
     */
    public $capitals = false;

    /**
     * Vous pouvez aussi surcharger cette méthode pour avoir des options obligatoires lors de la création d'une contrainte
     * Par exemple ici, on aurait pu dire que message et capitals sont des options qu'il faut absolument renseigner lors de la création :
     * new GmailConstraint(['message' => ..., 'capitals' => false])
     * 
     * Sans quoi il y aurait une exception. Mais nous ne le ferons pas ici
     */
    public function getRequiredOptions()
    {
        // return ['message', ''capitals']
    }
}
