<?php

include 'tree.php';
class FPGrowth
{
    protected $support = 0;

    private $dataset;
    public $duration = 0;


    /**
     * FPGrowth constructor.
     * @param $support 1, 2, 3 ...
     * @param $confidence 0 ... 1
     */
    public function __construct($dataset,$support)
    {
        $this->support = $support;

        $this->dataset = $dataset;
    }
    public function run(){
        return $this->patterns = $this->toFrequentItemset($this->findFrequentPatterns($this->dataset, $this->support));
    }

    protected function findFrequentPatterns($transactions, $support_threshold)
    {
        $tree = new FPTree($transactions, $support_threshold, null, null);
        return $tree->minePatterns($support_threshold);
    }
    protected function toFrequentItemset($array){

        $freq = [];$i = 0;
        foreach($array as $key => $_){

            $items = explode(",",$key);
            if(!array_key_exists($i,$freq)){
                $freq += [
                    $i => []
                ];
            }
            foreach($items as $item){
                array_push($freq[$i],$item); 
            }
            $i++;
        }
        
        return $freq;
    }

    

    public static function iter($var)
    {
        switch (true) {
            case $var instanceof \Iterator:
                return $var;

            case $var instanceof \Traversable:
                return new \IteratorIterator($var);

            case is_string($var):
                $var = str_split($var);

            case is_array($var):
                return new \ArrayIterator($var);

            default:
                $type = gettype($var);
                throw new \InvalidArgumentException("'$type' type is not iterable");
        }

        return;
    }

    public static function combinations($iterable, $r)
    {
        $pool = is_array($iterable) ? $iterable : iterator_to_array(self::iter($iterable));
        $n = sizeof($pool);

        if ($r > $n) {
            return;
        }

        $indices = range(0, $r - 1);
        yield array_slice($pool, 0, $r);

        for (; ;) {
            for (; ;) {
                for ($i = $r - 1; $i >= 0; $i--) {
                    if ($indices[$i] != $i + $n - $r) {
                        break 2;
                    }
                }

                return;
            }

            $indices[$i]++;

            for ($j = $i + 1; $j < $r; $j++) {
                $indices[$j] = $indices[$j - 1] + 1;
            }

            $row = [];
            foreach ($indices as $i) {
                $row[] = $pool[$i];
            }

            yield $row;
        }
    }
}