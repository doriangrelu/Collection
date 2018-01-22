# Collection

----------------------------------

## 1. Description

L'object Collection hérite de la logique des collections d'objets communs à différents langages comme JAVA, C# etc...
L'avantage d'utiliser ce type d'objet est de pouvoir appliquer une certaine logique, comme le classement, et pourquoi pas la pile et la file de priorité.
Une implémentation de l'interface Comparable avec la méthode compareTo a été efefctuée. Cela permet de classer automatiquement si on le désire, une collection d'objet de même type. 
Ce concept est hérité de JAVA et permet de classer rapidement et simplement une Collection, sans avoir à écrire des tones et des tones de code. 
De plus le trie effectué sur la Collection utilise l'algorithme Quick Sort qui offre une bonne compléxité pour ce type d'opération.

---------------------------------

## 2. Installation

Pour utiliser cette librairie, il suffit d'utiliser composer:

`
composer require dorian/collection
`

---------------------------------

## 3. Utilisation
1. Contexte

    Il est impératif d'utiliser *l'autoloader* de **compsoer**:
    
    `require '/vendor/autoload.php';`

2. Création d'une nouvelle Collection simple sans paramètre:

    ```php
    use Dorian\Collection\Collection;
 
    $uneCollection= new Collection();
    $uneCollection->add("bonjour");   
    $uneCollection->add(22);   
    var_dump($uneCollection->toArray());
 
 
    /* Affiche 
    [
       "bonjour",
       22
    ]
    */
    ```
    