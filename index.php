<?php 
require 'algorithms/eclat.php';
$dataset = [
    [1, 3],
    [1, 2, 5],
	[1, 2, 3],
	[1, 2, 3, 4],
	[3, 4],
];

$eclate = new Eclate($dataset,2);
echo'<pre>';
$eclate->run();

echo '<style>
table {margin-bottom:50px;font-family: mono;border-collapse: collapse;border: 1px solid;}
td,th{padding:6px 20px;border: 1px solid;}
.red{background-color:red}
</style>';

echo '<h2>1) Dataset:</h2>';
echo '<table border>';
foreach($dataset as $id => $rows){
    echo '<tr>';
    echo '<th>T<sub>'.$id.'</sub></th>';
    foreach($rows as $val){
        echo '<td>'.$val.'</td>';
    }
    echo '</tr>';
}
echo '</table>';
echo '<h2>1) Trasform vertical:</h2>';

echo '<table border>';
$tmpcount = 1;
foreach($eclate->khorti as $row){
    if(count($row['itemset']) != $tmpcount){
        echo '</table>';
        echo '<table>';
        $tmpcount = count($row['itemset']);
    }

    echo '<tr>';

    echo '<th>';
    foreach($row['itemset'] as $item){
        echo $item.', ';
    }
    echo '</th>';

    
    echo '<td>';
    foreach($row['tidset'] as $val){
        echo 'T<sub>'.$val.'</sub> ';
    }
    echo '</td>';
        
    
    echo '</tr>';
    
}
echo '</table>';


