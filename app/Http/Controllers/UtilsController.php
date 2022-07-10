<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UtilsController extends Controller
{
    //

    public static function checkInventoryAvailibility($qty, $inventory_id)
    {
        $carted = Cart::where('inventory', $inventory_id)->sum('quantity');
        $total_qty = Inventory::where('id', $inventory_id)->value('quantity');
        $available = $total_qty - $carted;
        return $available >= (int) $qty && $total_qty ? (bool) true : (int) $available;
    }

    // utils
    public static function UniqueID($len, $table, $field = 'id')
    {
        $id = UtilsController::genId($len);
        $try = DB::table($table)->where($field, $id)->first();

        return !$try ? $id : UtilsController::UniqueID($len, $table, $field);
    }

    private static function genId($len)
    {
        $id = "";
        $i = 0;

        while ($i < $len) {
            $id .= mt_rand(1, 9);
            $i++;
        }

        return (int) $id;
    }
}
