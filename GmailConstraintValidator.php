<?php

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * LA CLASSE DE VALIDATION :
 * ------------
 * C'est cette classe qui comporte la logique de validation
 */
class GmailConstraintValidator  extends ConstraintValidator
{
    /**
     * LA FONCTION VALIDATE :
     * -----------
     * Elle va recevoir deux choses :
     * - $value : la valeur à valider (exemple "lior@hotmail.com" ou "lior@GMAIL.COM" ou "lior@hotmail.com" ou même parfois "")
     * - $constraint : l'instance de la contrainte (qui porte donc les options !)
     * 
     * Son but est de vérifier que la $value correspond bien à la contrainte et ses options
     */
    public function validate($value, Constraint $constraint)
    {
        // Si nous n'avons pas de value ou que c'est une chaine vide, on ne s'en occupe pas
        // Notez qu'on ne créé pas d'erreur, on laisse passer ! Ce n'est pas à nous de faire une erreur si la chaine est vide, nous
        // On ne s'occupe que du fait que ce soit du GMAIL :)
        if ($value === null || $value === '') {
            return;
        }

        // Par défaut, on veut vérifier que l'adresse se finit bien par @GMAIL.COM
        $regExp = '/@GMAIL\.COM$/';

        // Si l'option "capitals" est false, alors on se fout que ce soit en majusules ou pas
        if (!$constraint->capitals) {
            // On transforme donc la valeur en miniscules
            $value = strtolower($value);
            // Et on veut simplement vérifier si ça se finit par @gmail.com
            $regExp = '/@gmail\.com$/';
        }

        // LOGIQUE DE VALIDATION :
        // Si la valeur ne correspond pas à l'expression régulière, alors il y a erreur
        if (!preg_match($regExp, $value)) {
            // Construction de l'erreur
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
