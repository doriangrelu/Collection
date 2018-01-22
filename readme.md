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

    Il est impératif d'utiliser *l'autoloader* de **composer**:
    
    Dans un premier temps il est donc necessaire d'initialiser composer via la commande
     `composer init`. Cette commande permet l'initialisation d'un projet utilisant ce gestionnaire de dépendance.
    Il faut donc ensuite executer la commande suivante:
    
    ```
    composer require dorian/collection
    ```
    
    Il faut ensuite inclre l'autloader de *composer* en intégrant dans votre fichier **PHP**:
    
    ```
    require '/vendor/autoload.php';
    ```

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
    
    Le constructeur de la Class **Collection** prends peut prendre deux tableaux en 
    paramètre. 
    
    ```php
    /**
    * Collection constructor.
    * @param array $array
    * @param array|null $params
    */
    public function __construct(?array $array = [], ?array $params=[]);
    ```    
    
    **Le premier** paramètre `$array` peut créer une collection à partie d'un tableau existant.
    
    **Le second** paramètre `$params` prends en paramètres les différents paramètres 
    comme le type d'objets contenu dans la collection, ou bien si l'on veut activer l'auto-sort.
    Voici la structure du tableau de paramètres: 
    
    ```php
    private $params = [
       "isJson" => false, //Création de la collection à partir d'une chaine json
       "sorted" => false, //auto-sort désactivé par défaut 
       "comparable" => false, //Trier des éléments de type Comparable
       "type" => null //Type d'éelements de la collection
     ];
    ```