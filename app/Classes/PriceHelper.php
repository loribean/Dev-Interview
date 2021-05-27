<?php

namespace App\Classes;

class PriceHelper
{
    /*
     * Todo: Coding Test for Technical Hires
     * Please read the instructions on the README.md
     * Your task is to write the functions for the PriceHelper class
     * A set of sample test cases and expected results can be found in PriceHelperTest
     */

    /**
     * Task: Write a function to return the unit price of an item based on the quantity
     *
     * Question:
     * If I purchase 10,000 bicycles, the unit price of the 10,000th bicycle would be 1.50
     * If I purchase 10,001 bicycles, the unit price of the 10,001st bicycle would be 1.00
     * If I purchase 100,001 bicycles, what would be the unit price of the 100,001st bicycle?
     *
     * @param int $qty
     * @param array $tiers
     * @return float
     */
    public static function getUnitPriceTierAtQty(int $qty, array $tiers): float
    {
        // if negative or zero, straight return
        if($qty <= 0){
            return 0;
        }
        $qtyTiers = array_keys($tiers);
        $prices = array_values($tiers);
        // if dont qualify for tier2, means its tier 1
        if($qty < $qtyTiers[1]){
            return $prices[0];
        }
    
        $quantityForTier2 = $qty - ($qtyTiers[1]) ;
        $quantityForTier3 = $qty - $qtyTiers[2];
        //if  qualify for tier 3, means its tier 3
        if($quantityForTier3 >= 0){
            return $prices[2]; 
        }
        // else, its tier 2
            return $prices[1];
            
        
    }

    /**
     * Task: Write a function to return the total price based on the quantity
     *
     * Question:
     * If I purchase 10,000 bicycles, the total price would be 1.5 * 10,000 = $15,000
     * If I purchase 10,001 bicycles, the total price would be (1.5 * 10,000) + (1 * 1) = $15,001
     * If I purchase 100,001 bicycles, what would the total price be?
     *
     * @param int $qty
     * @param array $tiers
     * @return float
     */
    public static function getTotalPriceTierAtQty(int $qty, array $tiers): float
    {
        // if negative or zero, straight return
        
        if($qty <= 0){
            return 0;
        }
        
   
        //if qty less than tier 2, just return tier 1 * qty
        if($qty < $qtyTiers[1]){
            return $qty * $prices[0];
          }

        //if qty less than tier 3, just return tier 1 * first 10k, tier 2 * $qty - 10k
        if($qty < $qtyTiers[2]){
            return $prices[0]* ($qtyTiers[1]-1) + $prices[1] * ($qty - $qtyTiers[1] + 1);
        }

        //if we are here means that we have reached tier 3! so, return tier 1 * 10k, tier 2 * 90k and tier 3 * qty - 90k + 1 ie: 101,000 = 10k @ 1.5, 90k @ 1 and 1 @ 0.5
       return $prices[0]* ($qtyTiers[1]-1) + $prices[1] * ($qtyTiers[2] - $qtyTiers[1]) + $prices[2] * ($qty - $qtyTiers[2] +1 );
    }

    /**
     * Task: Write a function to return an array of prices at each quantity
     *
     * Question A:
     * A user purchased 933, 22012, 24791 and 15553 bicycles respectively in Jan, Feb, Mar, April
     * The management would like to know how much to bill this user for each of those month.
     * This user is on a special pricing tier where the quantity does not reset each month and is thus CUMULATIVE.
     *
     * Question B:
     * A user purchased 933, 22012, 24791 and 15553 bicycles respectively in Jan, Feb, Mar, April
     * The management would like to know how much to bill this user for each of those month.
     * This user is on the typical pricing tier where the quantity RESETS each month and is thus NOT CUMULATIVE.
     *
     */
    public static function getPriceAtEachQty(array $qtyArr, array $tiers, bool $cumulative = false): array
    {
    $monthlyCharges =[];

       if($cumulative){

         //22021 + 933 = 22954 (cumulative)
        //find cost of 22954 - cost of 933 thats the cost of the month 2
        //22954 + 24791 = 47745 (cumulative)
        //find the cost of 47745 - 22954 = cost of month 3

        // $cumulativeCosts =  [cumulativeCostMonth1 , cumulativeCostMonth2, cumulativeCostMonth3 ...]
        //$realcost = [cumulativeCostMonth1 - CumulativeCostMonth 0, cumulativeCostMonth2 - CumulativeCostMonth 1, cumulativeCostMonth3 - CumulativeCostMonth 2, ...]

        // to use one foreach loop so that its more efficient, we keep track of three arrays, cumulativeCost, cumulativeBikes and monthlyCharges
    

           //3a
           $cumulativeCosts = [];
           $cumulativeBikes = [];
         
           foreach($qtyArr as $index => $value){
                if($index === 0){
                    $costForMonth1 = PriceHelper::getTotalPriceTierAtQty($value, $tiers);
                     array_push($cumulativeCosts, $costForMonth1);
                     array_push($cumulativeBikes,$value); 
                     array_push($monthlyCharges,  $costForMonth1);
                } else {
                    $cumulativeBikeForMonth = $value + $cumulativeBikes[$index-1];
                   array_push( $cumulativeBikes ,$cumulativeBikeForMonth);
                     array_push($cumulativeCosts,PriceHelper::getTotalPriceTierAtQty($cumulativeBikeForMonth,$tiers));
                     array_push( $monthlyCharges, $cumulativeCosts[$index]- $cumulativeCosts[$index-1]);
                }
           }

           return $monthlyCharges;


       }

       //3b NOT CUMULATIVE
       foreach($qtyArr as $monthlyBike){
          $monthlyCharge = PriceHelper::getTotalPriceTierAtQty($monthlyBike, $tiers);
          array_push($monthlyCharges , $monthlyCharge);
       }

       return $monthlyCharges;


    }

}
