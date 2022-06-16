<style>
    td,th{
        padding: 5px 20px;
    }
</style>
<pre>
<?php 
require 'algorithms/eclat.php';
require 'algorithms/apriori.php';
require 'algorithms/fbgrowth/fbgrowth.php';
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
$aprioriAssRules = $apriori->getAssociationRules();

$fbgrowth = new FPGrowth($dataset,2,60);
$fbgrowthAssRules = $fbgrowth->AssociationRules();

?>
<table>
    <tr>
        <th>Apriori</th>
        <th>Fb Growth</th>
        <th>Eclate</th>
    </tr>
    <tr>
        <td>
            <?php 
            foreach($aprioriAssRules as $a =>$rules){
                foreach($rules as $b => $confidence){
                    echo $a.' => '.$b.' = '.$confidence.'%<br>';
                }
            }
            ?>
        </td>
        <td>
            <?php 
            foreach($fbgrowthAssRules as $a =>$rules){
                foreach($rules as $b => $confidence){
                    echo $a.' => '.$b.' = '.$confidence.'%<br>';
                }
            }
            ?>
        </td>
        <td>
            <?php 
            foreach($eclateAssRules as $a =>$rules){
                foreach($rules as $b => $confidence){
                    echo $a.' => '.$b.' = '.$confidence.'%<br>';
                }
            }
            ?>
        </td>
    </tr>
    <tr>
        <th><?=$apriori->duration?>s</th>
        <th><?=$fbgrowth->duration?>s</th>
        <th><?=$eclate->duration?>s</th>
    </tr>
</table>