<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


/**
 * @group Cart
 * 
 */

class CartController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Add Inventory To Cart
     * 
     * @authenticated
     * 
     * @bodyParam inventory_id integer required Inventory ID . Example: 1234
     * @bodyParam quantity integer required  Quantity. Example: 1
     * 
     * @response{
     *  'status' => "success",
     *  'message' => "Inventory added to cart successfully, Cart ID: 1234"
     * }
     */

    public function addToCart(Request $request)
    {
        $request->validate([
            'inventory_id' => 'required|integer',
            'quantity' => 'required|integer|min:1'
        ]);

        $id = UtilsController::UniqueID(4, 'carts');

        $user_id = $request->user()->id;
        $available_qty = UtilsController::checkInventoryAvailibility($request->quantity, $request->inventory_id);
        $user_type = $request->user()->user_type == 'User';
        // disallow admin from adding to cart
        if (gettype($available_qty) == 'boolean' && $user_type) {
            Cart::create([
                'id' => $id,
                'user_id' => $user_id,
                'inventory' => $request->inventory_id,
                'quantity' => $request->quantity,
            ]);

            return response()->json([
                'status' => "success",
                'message' => "Inventory added to cart successfully, Cart ID: {$id}"
            ], status: 200);
        } else {
            return $user_type ?
                response()->json([
                    'status' => "error",
                    'message' => "Admin Cannot Add Item To Cart"
                ], status: 501)
                : response()->json([
                    'status' => "error",
                    'message' => "Unable to Add Inventory to Cart, Available Quantity: {$available_qty}"
                ], status: 501);
        }
    }

    /**
     * List Cart Inventories
     * 
     * @authenticated
     * 
     * @response{
     *  'status' => "success || error",
     *  'message' => "success || error message"
     *  'data' => []
     * }
     */
    public function listCart()
    {
        $cart = Cart::join('inventories', 'carts.inventory', '=', 'inventories.id')
            ->where('user_id', Auth::user()->id)->get(['carts.id', 'inventories.price', 'inventories.name', 'carts.quantity']);
        $cart_count = count($cart);

        $grandTotal = 0;

        // total and grand total cal
        foreach ($cart as $item) {
            $item['total'] = (float) $item['quantity'] * $item['price'];
            $grandTotal += $item['total'];
        }


        return response()->json([
            'status' => "success",
            'message' => $cart_count > 0 ? "{$cart_count} item(s) in cart, Total Price: {$grandTotal}" : "Cart Empty",
            'data' => $cart
        ], status: 200);
    }


    /**
     * Delete Inventory From Cart
     * 
     * @authenticated
     * 
     * @urlParam cart_id integer required Cart ID . Example: 1234
     * 
     * @response{
     *  'status' => "success || error",
     *  'message' => "success || error message"
     * }
     */
    public function deleteFromCart($cart_id)
    {
        $del = Cart::where([
            ['id', $cart_id],
            ['user_id', Auth::user()->id]
        ])->first();

        if (!$del) {
            return response()->json([
                'status' => "error",
                'message' => "Invalid Cart ID !!"
            ], status: 501);
        }

        return $del->delete() ? response()->json([
            'status' => "success",
            'message' => "Cart #{$cart_id} deleted !!"
        ], status: 200) : response()->json([
            'status' => "error",
            'message' => "Unable to delete Cart !!"
        ], status: 501);
    }


    /**
     * Update Existing Inventory In Cart
     * 
     * @authenticated
     * 
     * @urlParam cart_id integer required Cart ID . Example: 1234
     * @bodyParam quantity integer required Quantity . Example: 1234
     * 
     * @response{
     *  'status' => "success || error",
     *  'message' => "success || error message"
     * }
     */
    public function updateCart(Request $request, $cart_id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $item = Cart::where([
            ['id', $cart_id],
            ['user_id', $request->user()->id]
        ])->first();

        if (!$item) return response()->json([
            'status' => "error",
            'message' => "Invalid Cart ID"
        ], status: 404);


        $available_qty = UtilsController::checkInventoryAvailibility($request->quantity, $item->inventory);
        if (gettype($available_qty) === "integer") return response()->json([
            'status' => "error",
            'message' => "Unable To Update Cart, Available Quantity: {$available_qty}"
        ], status: 501);

        return $item->update($request->all()) ?  response()->json([
            'status' => "success",
            'message' => "Cart Successfully Updated"
        ], status: 200) :
            response()->json([
                'status' => "error",
                'message' => "Unable To Update Cart, Try Again"
            ], status: 200);
    }
}
