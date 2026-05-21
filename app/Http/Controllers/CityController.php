<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CityController extends Controller
{
    /**
     * Get all cities
     */
    public function index()
    {
        $cities = City::latest()->get();

        return $this->success(
            $cities,
            'Cities fetched successfully'
        );
    }

    /**
     * Get single city
     */
    public function show(string $id)
    {
        $city = City::find($id);

        if (!$city) {
            return $this->notFound('City');
        }

        return $this->success(
            $city,
            'City fetched successfully'
        );
    }

    /**
     * Create city
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return $this->validationError(
                $validator->errors()
            );
        }

        $city = City::create([
            'name' => $request->name
        ]);

        return $this->created(
            $city,
            'City created successfully'
        );
    }

    /**
     * Update city
     */
    public function update(Request $request, string $id)
    {
        $city = City::find($id);

        if (!$city) {
            return $this->notFound('City');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return $this->validationError(
                $validator->errors()
            );
        }

        $city->update([
            'name' => $request->name
        ]);

        return $this->success(
            $city,
            'City updated successfully'
        );
    }

    /**
     * Delete city
     */
    public function destroy(string $id)
    {
        $city = City::find($id);

        if (!$city) {
            return $this->notFound('City');
        }

        $city->delete();

        return $this->success(
            [],
            'City deleted successfully'
        );
    }


    public function areas($id)
    {
        $areas = Area::where('city_id', $id)->get();

        return response()->json([
            'data' => $areas
        ]);
    }
}