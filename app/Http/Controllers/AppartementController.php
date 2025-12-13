<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppartementRequest;
use App\Models\Appartement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AppartementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {}

    /**
     * Store a newly created resource in storage.
     */

    public function store(StoreAppartementRequest $request)
    {
        $paths = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $paths[] = $image->store('appartements', 'public');
            }
        }
        //  $user_id=Auth()
        $store = Appartement::create(array_merge(
            $request->validated(),
            [
                'images' => $paths,
                'user_id' => Auth::user()->id
            ]
        ));

        return response()->json([
            'message' => 'Appartement created successfully.',
            'data'    => $store,
        ], 201);
    }

    public function show(Appartement $appartement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Appartement $appartement)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $appartement = Appartement::findOrFail($id);

        // حذف الصور من التخزين
        if ($appartement->images && is_array($appartement->images)) {
            foreach ($appartement->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        // حذف السجل
        $appartement->delete();

        return response()->json([
            'message' => 'Appartement deleted successfully.'
        ], 200);
    }
}
