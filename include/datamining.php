<?php
require 'algorithms/eclat.php';
require 'algorithms/apriori.php';
require 'algorithms/fbgrowth/fbgrowth.php';
class DataMine{
    public $support = 1;
    public $confidence = 0;
    public $dataset = [];
    public $duration = 0;
    public $assRulesDuration = 0;
    public $algorithm;

    public $rules = [];
    public $frequentItemSets = [];

    public function __construct($dataset,$support,$confidence,$algorithm){
        $this->dataset = $dataset;
        $this->support = $support;
        $this->confidence = $confidence;
        $this->algorithm = $algorithm;
        $this->mine();
    }

    public function mine(){

        $algo = [];

        switch($this->algorithm){
            case "Apriori" :
                $algo = new Apriori($this->dataset,$this->support,$this->confidence);
            break;
            case "Eclate" :
                $algo = new Eclate($this->dataset,$this->support,$this->confidence);
            break;
            case "FBGrowth" :
                $algo = new FPGrowth($this->dataset,$this->support,$this->confidence);
            break;
            default : 
                $algo = new Apriori($this->dataset,$this->support,$this->confidence);
            break;
        }

        $start = microtime(true);
        $this->frequentItemSets = $algo->run();
        $this->duration = round((microtime(true) - $start)*1000,4);

        $start = microtime(true);
        $this->generateAssociationRules($this->frequentItemSets,$this->confidence/100);
        $this->assRulesDuration = round((microtime(true) - $start)*1000,4);

    }

    protected function generateAssociationRules()
    {
        $assRules = new AssociationRules($this->frequentItemSets,$this->support,$this->confidence);
        $assRules->makeTable($this->dataset);
        $this->rules = $assRules->process();
    }

    public function printRules(){
        $tmp = [];
        $result = '';
        foreach($this->rules as $a =>$rules){
            foreach($rules as $b => $confidence){
                $tmp += [
                    $a.' => '.$b => $confidence,
                ];
                
            }
        }
        arsort($tmp);
        foreach($tmp as $k => $c){
            $result .= $k .' = '.$c.'%<br>';
        }
        return $result;
    }
    public function printFrequentItemSets(){
        $freq = '';
        foreach($this->frequentItemSets as $items){
            $freq .= '{';
            foreach($items as $i => $item){

                $freq .=$item;
                if(count($items)-1 > $i) $freq .= ',';
            }
            $freq .= '}<br>';
        }
        return $freq;
    }
}