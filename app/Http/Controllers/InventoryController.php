<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;

/**
 * @group Inventories
 * 
 */

class InventoryController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth:admin', ['except' => 'listInventory']);
    }

    /**
     * List Inventory
     * 
     * @authenticated
     * 
     * 
     * @response{
     *  'status' => "success || error",
     *  'message' => "sucess || error message",
     *  'data' => []
     * }
     */
    public function listInventory()
    {
        $inventory = Inventory::all(['id', 'name', 'price']);
        return response()->json([
            'status' => 'success',
            'message' => count($inventory) > 0 ? 'Inventory List' : 'Inventory Empty !!',
            'data' => $inventory
        ]);
    }

    /**
     * Create Inventory
     * 
     * @authenticated
     * 
     * @bodyParam name string required Inventory Name . Example: Samsung S21 Ultra
     * @bodyParam price integer required  Inventory Price. Example: 1450
     * @bodyParam quantity integer required  Inventory Supplied Quantity. Example: 120
     * 
     * @response{
     *  'status' => "success || error",
     *  'message' => "Inventory Created Successfully, Inventory id: #334"
     * }
     */

    public function addInventory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:inventories',
            'price' => 'required|numeric',
            'quantity' => 'required|numeric'
        ]);
        $id = UtilsController::UniqueID(3, 'inventories');
        try {
            $user = Inventory::create([
                'id' => $id,
                'name' => $request->name,
                'price' => $request->price,
                'quantity' => $request->quantity,
            ]);
            // 
            return response()->json([
                'status' => 'success',
                'message' => "Inventory Created Successfully, Inventory id: {$id}",
            ], 201);
            //code...
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'message' => "Unable to create Inventory",
            ], 500);
        }
    }

    /**
     * Delete Inventory
     * 
     * @authenticated
     * 
     * @urlParam inventory_id integer required Inventory ID
     * 
     * @response{
     *  'status' => "success || error",
     *  'message' => "sucess || error message"
     * }
     */

    public function deleteInventory($id)
    {
        $check = Inventory::destroy($id);

        if ($check)
            return  response()->json([
                'status' => "success",
                'data' => 'Inventory Deleted Successfully'
            ], status: 200);
        else
            return  response()->json([
                'status' => "error",
                'data' => 'Unable to delete inventory'
            ], status: 501);
    }

    /**
     * View Specific Inventory
     * 
     * @authenticated
     * 
     * @urlParam inventory_id integer required Inventory ID.
     * 
     * @response{
     *  'status' => "success || error",
     *  'message' => "sucess || error message"
     * }
     */

    public function viewInventory($id)
    {
        $item = Inventory::find($id);

        return  response()->json([
            'status' => gettype($item) != 'NULL' ? "success" : "error",
            'data' => gettype($item) != 'NULL' ? $item : "Inventory Not Found"
        ], status: 200);
    }

    /**
     * Update Inventory
     * 
     * @authenticated
     * 
     * @urlPtaram inventory_id integer required Inventory ID . Example: 1234
     * @bodyParam name string required Inventory Name . Example: Samsung S21 Ultra
     * @bodyParam price integer required  Inventory Price. Example: 1450
     * @bodyParam quantity integer required  Inventory Supplied Quantity. Example: 120
     * 
     * @response{
     *  'status' => "success || error",
     *  'message' => "sucess || error message"
     * }
     */

    public function updateInventory(Request $request, $id)
    {
        $request->validate([
            'name' => 'string',
            'price' => 'numeric',
            'quantity' => 'numeric',
        ]);

        $item = Inventory::find($id);

        if ($item) {
            $item->update($request->all());

            return  response()->json([
                'status' => "success",
                'message' => 'Inventory Updated',
                'data' => $item,
            ], status: 200);
        } else {
            return  response()->json([
                'status' => "success",
                'message' => 'Unable to Update Inventory (Not Found)'
            ], status: 200);
        }
    }
}
