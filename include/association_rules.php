<?php

class AssociationRules
{
    private $freq;
    public $rules;
    public $table       = array();
    private $allsups     = array();
    private $keys        = array();
    private $delimiter = ',';

    private $minSup      = 0;
    private $minConf     = 0;

    private $allitem   = array();
    private $phase       = 1;

    public function __construct($freq,$minsup,$minconf)
    {
        $this->freq = $freq;
        $this->minConf = $minconf;
        $this->minSup = $minsup;
        
    }

    public function process()
    {
        foreach ($this->freq as $k => $v) {

            $arr     = $v;
            $subsets = $this->subsets($arr);
            
            $num     = count($subsets);
            for ($i = 0; $i < $num; $i++) {
                for ($j = 0; $j < $num; $j++) {
                    
                    if ($this->checkRule($subsets[$i], $subsets[$j])) {

                        $n1 = $this->realName($subsets[$i]);
                        $n2 = $this->realName($subsets[$j]);

                        //echo 'n1:'.$n1.' n2:'.$n2.'..<br>';
                        
                        $scan = $this->scan($this->combine($subsets[$i], $subsets[$j]));
                        //echo $scan.'<br>';
                        $c1   = $this->confidence($this->scan($subsets[$i]), $scan);
                        $c2   = $this->confidence($this->scan($subsets[$j]), $scan);

                        if ($c1 >= $this->minConf) {
                            $result[$n1][$n2] = $c1;
                        }

                        if ($c2 >= $this->minConf) {
                            $result[$n2][$n1] = $c2;
                        }

                        $checked[$n1 . $this->delimiter . $n2] = 1;
                        $checked[$n2 . $this->delimiter . $n1] = 1;
                    }
                }
            }
        }
        return $this->rules = $result;
    }
    private function subsets($items)
    {

        $result  = array();
        $num     = count($items);
        $members = pow(2, $num);
        for ($i = 0; $i < $members; $i++) {
            $b   = sprintf("%0" . $num . "b", $i);
            $tmp = array();
            for ($j = 0; $j < $num; $j++) {
                if ($b[$j] == '1') {
                    $tmp[] = $items[$j];
                }
            }

            if ($tmp) {
                sort($tmp);
                $result[] = $tmp;
            }
        }
        return $result;
    }
    //1-2=>2-3 : false
    //1-2=>5-6 : true
    private function checkRule($a, $b)
    {
        $a_num = count($a);
        $b_num = count($b);
        for ($i = 0; $i < $a_num; $i++) {
            for ($j = 0; $j < $b_num; $j++) {
                if ($a[$i] == $b[$j]) {
                    return false;
                }
            }
        }

        return true;
    }
    private function realName($arr)
    {
        $result = '';

        $num = count($arr);
        for ($j = 0; $j < $num; $j++) {
            if ($j) {
                $result .= $this->delimiter;
            }

            $result .= $this->keys['k->v'][$arr[$j]];
        }

        return $result;
    }
    private function scan($arr, $implodeArr = '')
    {
        $cr = 0;

        if ($implodeArr) {
            if (isset($this->allsups[$implodeArr])) {
                return $this->allsups[$implodeArr];
            }
        } else {
            sort($arr);
            $implodeArr = implode($this->delimiter, $arr);
            if (isset($this->allsups[$implodeArr])) {
                return $this->allsups[$implodeArr];
            }
        }

        $num  = count($this->table);
        $num1 = count($arr);
        for ($i = 0; $i < $num; $i++) {
            $bool = true;
            for ($j = 0; $j < $num1; $j++) {
                if (!isset($this->table[$i][$arr[$j]])) {
                    $bool = false;
                    break;
                }
            }

            if ($bool) {
                $cr++;
            }
        }

        $this->allsups[$implodeArr] = $cr;

        return $cr;
    }
    private function combine($arr1, $arr2)
    {
        $result = array();

        $num  = count($arr1);
        $num1 = count($arr2);
        for ($i = 0; $i < $num; $i++) {
            if (!isset($result['k'][$arr1[$i]])) {
                $result['v'][] = $arr1[$i];
                $result['k'][$arr1[$i]] = 1;
            }
        }

        for ($i = 0; $i < $num1; $i++) {
            if (!isset($result['k'][$arr2[$i]])) {
                $result['v'][] = $arr2[$i];
                $result['k'][$arr2[$i]] = 1;
            }
        }

        return $result['v'];
    }
    private function confidence($sup_a, $sup_ab)
    {
        //echo $sup_ab.'/'.$sup_a.'<br>';
        return round(($sup_ab / $sup_a) * 100, 2);
    }
    public function printAssociationRules()
    {
        $no = 1;
        if ($this->rules == null) {
            echo "<h3>Tidak ada aturan asosiasi yang terbentuk </h3>";
        } else {
            echo "<h3 align='center'>Association Rules yang terbentuk</h3><th>";

            echo
            "<table width='100%' height='80' class='table table-hover'>
			<tr>
			<th width='17%' class='col-md-1'>NO</th>
			<th width='17%' class='col-md-5'>Association Rule</th>
			<th width='12%' class='col-md-2'>Confidence</th>
			</tr>";

            foreach ($this->rules as $a => $arr) {
                foreach ($arr as $b => $conf) {
                    echo
                    "<tr bgcolor='#E6E6E6'><td>$no</td><td>$a=> $b</td><td style='color:blue' width='100px'>$conf%</td></tr>";
                    echo "<tr><td colspan='2'>Dari seluruh pelanggan yang membeli $a, $conf% juga membeli $b</td></tr>";
                    $no++;
                }
            }
            echo "</tbody><table>";
        }
    }
    public function makeTable($db)
    { 
        $table   = array();
        $array   = array();
        $counter = 1;
        //memeriksa apakah data transaksi berbentuk array 
        if(!is_array($db)){
            $db = file($db);
        }
  
       $num = count($db);

       for($i=0; $i<$num; $i++) 
          {
             $tmp  = $db[$i];//memecah item2 barang
             $num1 = count($tmp);//hitung jumlah item yg sudah dipecah
             $x    = array();
             for($j=0; $j<$num1; $j++) 
                {
                   $x = trim($tmp[$j]);//hilangkan spasi kiri kanan
                   if($x==='')
                      {
                         continue;
                      }
                      
                   if(!isset($this->keys['v->k'][$x]))
                      {
                         $this->keys['v->k'][$x]         = $counter;
                         $this->keys['k->v'][$counter]   = $x;
                         $counter++;
                      } 
               
                   if(!isset($array[$this->keys['v->k'][$x]]))
                      {
                         $array[$this->keys['v->k'][$x]] = 1; 
                         $this->allsups[$this->keys['v->k'][$x]] = 1;                        
                      }
                   else
                      {
                         $array[$this->keys['v->k'][$x]]++; 
                         $this->allsups[$this->keys['v->k'][$x]]++;
                      }
               
                   $table[$i][$this->keys['v->k'][$x]] = 1; 
                } 
          }
         
 
       $tmp = array();
       foreach($array as $item => $sup) 
          { 
             if($sup>=$this->minSup)
                {
                   
                   $tmp[] = array($item);
                }
          }
  

       $this->allitem[$this->phase] = $tmp;
       $this->table = $table;  
    }
}
