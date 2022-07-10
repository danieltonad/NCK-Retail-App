<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware(['auth:api', 'isAdmin'],['except' => ['listInventory']]);
    }

    public function listInventory()
    {
        $inventory = Inventory::all(['id','name','price']);
        return response()->json([
            'status' => 'success',
            'message' => count($inventory) > 0 ? 'Inventory List' : 'Inventory Empty !!',
            'data' => $inventory
        ]);
    }

    /**
     * Create the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * 
     * @return \Illuminate\Http\Response
     */

    public function addInventory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:inventories',
            'price' => 'required|numeric',
            'quantity' => 'required|numeric'
        ]);
        $id = $this->UniqueID(3, 'inventories');
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
     * Delete the specified resource in storage.
     *
     * @param    $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
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
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \App\Models\Inventory
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
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
