<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\Validator\Validation;

require __DIR__ . '/../vendor/autoload.php';

// On file l' autoloader à Doctrine pour qu'il puisse charger les classes d'annotations
$loader = require __DIR__ . '/../vendor/autoload.php';
AnnotationRegistry::registerLoader([$loader, 'loadClass']);

// Création du validateur
$validator = Validation::createValidatorBuilder()
    // On indique au Builder que le validateur qu'il va nous donner doit être capable de trouver les contraintes de validation sur un objet
    // en appelant une méthode static qui s'appelle loadValidatorMetadata (bien sur on aurait pu choisir un autre nom mais c'est une
    // convention)
    ->addMethodMapping('loadValidatorMetadata')
    // On indique au Builder que le validateur doit aussi être capable d'aller lire des configuration d'objets directement dans un fichier 
    // de configuration YAML
    ->addYamlMapping(__DIR__ . '/validation.yml')
    // On indique qu'on souhaite aussi être capable d'extraire les contraintes qui pèsent sur les propriétés d'un objet via les annotations
    // qui se trouvent au dessus des propriétés / méthodes
    ->enableAnnotationMapping()
    // Notre validateur est prêt, on demande au builder de nous l'offrir
    ->getValidator();
