<style>
    td{
        padding: 5px 20px;
    }
    .rotate{
        writing-mode: vertical-rl;
        -ms-writing-mode: tb-rl;
        transform: rotate(180deg);
        width: 30px;
    }

</style>
<pre>
<?php 
require 'include/association_rules.php';
require 'include/datamining.php';
$dataset = [
    [1, 2, 3, 4],
    [1, 4, 3],
    [2, 3],
    [1, 5, 3],
];
$minSupport =  2;
$confidence = 60;

$Apriori  = new DataMine($dataset,$minSupport,$confidence,'Apriori');
$FBGrowth = new DataMine($dataset,$minSupport,$confidence,'FBGrowth');
$Eclate   = new DataMine($dataset,$minSupport,$confidence,'Eclate');

?>
<table>
    <tr>
        <th></th>
        <th>Apriori</th>
        <th>Fb Growth</th>
        <th>Eclate</th>
    </tr>
    <tr>
        <th><span class="rotate">Frequent Items</span></th>
        <td><?=$Apriori->printFrequentItemSets()?></td>
        <td><?=$FBGrowth->printFrequentItemSets()?></td>
        <td><?=$Eclate->printFrequentItemSets()?></td>
    </tr>
    <tr style="background-color: #fffddd;">
        <th></th>
        <th><?=$Apriori->duration?> ms</th>
        <th><?=$FBGrowth->duration?> ms</th>
        <th><?=$Eclate->duration?> ms</th>
    </tr>
    <tr>
        <th><span class="rotate">Association rules</span></th>
        <td><?=$Apriori->printRules()?></td>
        <td><?=$FBGrowth->printRules()?></td>
        <td><?=$Eclate->printRules()?></td>
    </tr>
    <tr>
        <th></th>
        <th><?=$Apriori->assRulesDuration?> ms</th>
        <th><?=$FBGrowth->assRulesDuration?> ms</th>
        <th><?=$Eclate->assRulesDuration?> ms</th>
    </tr>
    
</table>