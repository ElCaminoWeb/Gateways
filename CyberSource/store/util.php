<?php
/* util.php *************************************************************/
/*                                                                      */
/* Copyright 2004. CyberSource Corporation.  All rights reserved.       */
/************************************************************************/

class BasketItem
{
        var $productName;
        var $productSKU;
        var $amount;
        var $quantity;

        function BasketItem( $productName, $productSKU, $amount, $quantity )
        {
                $this->productName = $productName;
                $this->productSKU = $productSKU;
                $this->amount = $amount;
                $this->quantity = $quantity;
        }
}

class Basket
{
        var $items;

        function Basket()
        {
                $items = array();
        }

        function addItem( $productName, $productSKU, $amount, $quantity )
        {
                $item = new BasketItem($productName, $productSKU, $amount, $quantity);
                $this->items[] = $item;
        }
}

//-----------------------------------------------------------------------------
function getArrayContent( $arr )
//-----------------------------------------------------------------------------
{
        $content = '';
        while (list( $key, $val ) = each( $arr ))
        {
                $content = $content . $key . ' => ' . $val . "<br>";
        }

        return( $content );
}

?>
