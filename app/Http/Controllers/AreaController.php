<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AreaController extends Controller
{
    /**
     * Get all areas
     */
    public function index()
    {
        $areas = Area::with('city')
            ->latest()
            ->get();

        return $this->success(
            $areas,
            'Areas fetched successfully'
        );
    }

    /**
     * Get single area
     */
    public function show(string $id)
    {
        $area = Area::with('city')->find($id);

        if (!$area) {
            return $this->notFound('Area');
        }

        return $this->success(
            $area,
            'Area fetched successfully'
        );
    }

    /**
     * Create area
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'city_id' => 'required|exists:cities,id',
            'name' => 'required|string|max:255',
            'fee' => 'nullable|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return $this->validationError(
                $validator->errors()
            );
        }

        $area = Area::create([
            'city_id' => $request->city_id,
            'name' => $request->name,
            'fee' => $request->fee ?? 0
        ]);

        return $this->created(
            $area,
            'Area created successfully'
        );
    }

    /**
     * Update area
     */
    public function update(Request $request, string $id)
    {
        $area = Area::find($id);

        if (!$area) {
            return $this->notFound('Area');
        }

        $validator = Validator::make($request->all(), [
            'city_id' => 'required|exists:cities,id',
            'name' => 'required|string|max:255',
            'fee' => 'nullable|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return $this->validationError(
                $validator->errors()
            );
        }

        $area->update([
            'city_id' => $request->city_id,
            'name' => $request->name,
            'fee' => $request->fee ?? 0
        ]);

        return $this->success(
            $area,
            'Area updated successfully'
        );
    }

    /**
     * Delete area
     */
    public function destroy(string $id)
    {
        $area = Area::find($id);

        if (!$area) {
            return $this->notFound('Area');
        }

        $area->delete();

        return $this->success(
            [],
            'Area deleted successfully'
        );
    }


        public function byCity($cityId)
    {
        return Area::where('city_id', $cityId)->get();
    }
}