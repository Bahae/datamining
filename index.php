<pre>
<?php 
require 'algorithms/eclat.php';
require 'algorithms/apriori.php';
require 'include/association_rules.php';
$dataset = [
    [1, 2, 3, 4],
    [1, 4, 3],
    [2, 3],
    [1, 5, 3],
];

$eclate = new Eclate($dataset,2,60);
$eclateAssRules = $eclate->AssociationRules();

$apriori = new Apriori($dataset,2,60);
$aprioriAssRules = $Apriori->getAssociationRules();

echo '<h1>Eclate</h1>';
foreach($eclateAssRules as $key =>$rules){
    foreach($rules as $id => $rule){
        echo $key.' => '.$id.' = '.$rule.'<br>';
    }
}

echo '<h1>Apriori</h1>';
foreach($aprioriAssRules as $key =>$rules){
    foreach($rules as $id => $rule){
        echo $key.' => '.$id.' = '.$rule.'<br>';
    }
}
