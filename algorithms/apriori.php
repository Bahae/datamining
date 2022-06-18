<?php

/**
 * Apriori Algorithm - الگوریتم اپریوری
 * PHP Version 5.0.0
 * Version 0.1 Beta 
 * @link http://vtwo.org
 * @author VTwo Group (info@vtwo.org)
 * @license GNU GENERAL PUBLIC LICENSE
 * 
 * 
 * '-)
 */
class Apriori
{
   private $delimiter   = ',';
   private $minSup      = 0;
   private $minConf     = 0;

   private $rules       = array();
   private $table       = array();
   private $allthings   = array();
   private $allsups     = array();
   private $keys        = array();
   private $freqItmsts  = array();
   private $phase       = 1;

   //maxPhase>=2
   private $maxPhase    = 20;

   private $fiTime      = 0;
   private $arTime      = 0;

   public $duration = 0;
   public function __construct($dataset, $minsupp, $conf)
   {
      $this->dataset = $dataset;
      $this->minSup = $minsupp;
      $this->minConf = $conf;
   }

   public function run()
   {
      return $this->freqItems($this->dataset);
   }

   private function makeTable($db)
   {
      $table   = array();
      $array   = array();
      $counter = 1;


      $num = count($db);
      for ($i = 0; $i < $num; $i++) {
         $tmp  = $db[$i];
         $num1 = count($tmp);
         $x    = array();
         for ($j = 0; $j < $num1; $j++) {
            $x = trim($tmp[$j]);
            if ($x === '') {
               continue;
            }

            if (!isset($this->keys['v->k'][$x])) {
               $this->keys['v->k'][$x]         = $counter;
               $this->keys['k->v'][$counter]   = $x;
               $counter++;
            }

            if (!isset($array[$this->keys['v->k'][$x]])) {
               $array[$this->keys['v->k'][$x]] = 1;
               $this->allsups[$this->keys['v->k'][$x]] = 1;
            } else {
               $array[$this->keys['v->k'][$x]]++;
               $this->allsups[$this->keys['v->k'][$x]]++;
            }

            $table[$i][$this->keys['v->k'][$x]] = 1;
         }
      }

      $tmp = array();
      foreach ($array as $item => $sup) {
         if ($sup >= $this->minSup) {

            $tmp[] = array($item);
         }
      }

      $this->allthings[$this->phase] = $tmp;
      $this->table = $table;
   }

   /**
        1. مقدار سوپریموم را با توجه به ورودی شناسه آیتمها شمارش می کند
    **/
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

   /**
        1. ترکیب دو آرایه و حذف مقادیر اضافی
    **/
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

   /**
        1. آیتم ستهای تکراری را بر می گرداند
    **/
   private function freqItemsets($db)
   {

      $this->makeTable($db);
      while (1) {
         if ($this->phase >= $this->maxPhase) {
            break;
         }

         $num = count($this->allthings[$this->phase]);
         $cr  = 0;
         for ($i = 0; $i < $num; $i++) {
            for ($j = $i; $j < $num; $j++) {
               if ($i == $j) {
                  continue;
               }

               $item = $this->combine($this->allthings[$this->phase][$i], $this->allthings[$this->phase][$j]);
               sort($item);
               $implodeArr = implode($this->delimiter, $item);
               if (!isset($this->freqItmsts[$implodeArr])) {
                  $sup = $this->scan($item, $implodeArr);
                  if ($sup >= $this->minSup) {
                     $this->allthings[$this->phase + 1][] = $item;
                     $this->freqItmsts[$implodeArr] = 1;
                     $cr++;
                  }
               }
            }
         }

         if ($cr <= 1) {
            break;
         }

         $this->phase++;
      }

      //زیر مجموعه های مربوط به مجموعه های بزرگتر را حذف می کند 
      foreach ($this->freqItmsts as $k => $v) {
         $arr = explode($this->delimiter, $k);
         $num = count($arr);
         if ($num >= 3) {
            $subsets = $this->subsets($arr);
            $num1    = count($subsets);
            for ($i = 0; $i < $num1; $i++) {
               if (count($subsets[$i]) < $num) {
                  unset($this->freqItmsts[implode($this->delimiter, $subsets[$i])]);
               } else {
                  break;
               }
            }
         }
      }
   }

   public function freqItems($db)
   {


      $this->freqItemsets($db);

      $tmp = [];

      foreach ($this->freqItmsts as $k => $v) {
         $arr     = explode($this->delimiter, $k);
         $subsets = $this->subsets($arr);
         foreach ($subsets as $subset) {
            array_push($tmp, $subset);
         }
      }
      $tmp = array_map("unserialize", array_unique(array_map("serialize", $tmp)));

      return $tmp;
   }
}
