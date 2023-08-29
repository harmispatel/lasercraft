<?php

namespace App\Http\Controllers;

use App\Models\Building;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BuildingController extends Controller
{
    // Display a listing of the resource.
    public function index()
    {
        // Shop ID
        $shop_id = isset(Auth::user()->hasOneShop->shop['id']) ? Auth::user()->hasOneShop->shop['id'] : '';

        $data['buildings'] = Building::where('shop_id',$shop_id)->get();
        return view('client.buildings.buildings',$data);
    }


    // Show the form for creating a new resource.
    public function create()
    {
        return view('client.buildings.create_building');
    }


    // Store a newly created resource in storage.
    public function store(Request $request)
    {

        $request->validate([
            'building_name' => 'required',
        ]);

        // Shop ID
        $shop_id = isset(Auth::user()->hasOneShop->shop['id']) ? Auth::user()->hasOneShop->shop['id'] : '';

        $building = new Building;
        $building->shop_id = $shop_id;
        $building->name = $request->building_name;
        $building->save();

        return redirect()->route('buildings')->with('success','New Building has been Created SuccessFully...');
    }


     // Remove the specified resource from storage.
     public function destroy(Request $request)
     {
         try
         {
            $building_id = $request->id;

            Building::where('id',$building_id)->delete();

             return response()->json([
                 'success' => 1,
                 'message' => 'Building has been Removed SuccessFully...',
             ]);
         }
         catch (\Throwable $th)
         {
             return response()->json([
                 'success' => 0,
                 'message' => 'Internal Server Error!',
             ]);
         }
     }

}
