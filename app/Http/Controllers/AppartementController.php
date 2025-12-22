<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppartementRequest;
use App\Models\Appartement;
use App\Notifications\NewAppartementNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AppartementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Right now we return ALL appartements
        $appartements = Appartement::all();

        // to only show approved appartements,
        // $appartements = Appartement::where('approval_status', 'approved')->get();

        return response()->json([
            'status' => 'success',
            'data'   => $appartements
        ], 200);
    }

    public function store(StoreAppartementRequest $request)
    {
        $paths = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $paths[] = $image->store('appartements', 'public');
            }
        }

        $appartement = Appartement::create(array_merge(
            $request->validated(),
            [
                'images'  => $paths,
                'user_id' => Auth::id(),
                'approval_status' => 'pending'

            ]
        ));

        foreach (\App\Models\Admin::all() as $admin) {
            $admin->notify(new NewAppartementNotification($appartement));
        }

        return response()->json([
            'message' => 'Appartement submitted successfully. Waiting for admin approval.',
            'data'    => $appartement,
        ], 201);
    }
    public function show(Appartement $appartement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $appartement = Appartement::findOrFail($id);

        // تحقق إنو المستخدم الحالي هو صاحب الشقة
        if ($appartement->user_id !== Auth::id()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized: You can only update your own appartement.'
            ], 403);
        }

        // قواعد التحقق
        $request->validate([
            'title'       => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'nullable|numeric|min:0',
            'space'       => 'nullable|integer|min:0',
            'rooms'       => 'nullable|integer|min:0',
            'floor'       => 'nullable|integer|min:0',
            'city'        => 'nullable|string|max:255',
            'area'        => 'nullable|string|max:255',
            'address'     => 'nullable|string|max:255',
            'images.*'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // تحديث الخصائص
        $appartement->fill($request->only([
            'title',
            'description',
            'price',
            'space',
            'rooms',
            'floor',
            'city',
            'area',
            'address',
        ]));

        // تحديث الصور إذا تم رفع صور جديدة
        if ($request->hasFile('images')) {
            $paths = [];
            foreach ($request->file('images') as $image) {
                $paths[] = $image->store('appartements', 'public');
            }
            $appartement->images = $paths;
        }

        $appartement->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Appartement updated successfully.',
            'data'    => $appartement
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */ public function destroy($id)
    {
        $appartement = Appartement::findOrFail($id);

        if ($appartement->owner->id !== Auth::id()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized: You can only delete your own appartement.'
            ], 403);
        }

        if ($appartement->images && is_array($appartement->images)) {
            foreach ($appartement->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $appartement->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Appartement deleted successfully.'
        ], 200);
    }



    public function filter(Request $request)
    {
        $query = Appartement::query();

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        if ($request->filled('area')) {
            $query->where('area', $request->area);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('rooms')) {
            $query->where('rooms', $request->rooms);
        }

        // ✅ فلترة على floor
        if ($request->filled('floor')) {
            $query->where('floor', $request->floor);
        }

        // ✅ فلترة على space
        if ($request->filled('space')) {
            $query->where('space', '>=', $request->space);
        }

        $appartements = $query->get();

        return response()->json([
            'status' => 'success',
            'data'   => $appartements
        ], 200);
    }
}
