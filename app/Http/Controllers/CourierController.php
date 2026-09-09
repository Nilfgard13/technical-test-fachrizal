<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreCourierRequest;
use App\Http\Requests\UpdateCourierRequest;
use App\Http\Resources\CourierResource;
use App\Models\Courier;

class CourierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sort = $request->input('sort', 'name');

        $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';

        $sortField = ltrim($sort, '-');

        $allowedSorts = [
            'name',
            'joined_at',
        ];

        if (!in_array($sortField, $allowedSorts, true)) {
            $sortField = 'name';
            $direction = 'asc';
        }

        $couriers = Courier::query()
            ->search($request->input('search'))
            ->filterLevel($request->input('level'))
            ->orderBy($sortField, $direction)
            ->paginate(
                $request->integer('per_page', 15)
            )
            ->withQueryString();

        return CourierResource::collection($couriers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCourierRequest $request)
    {
        $courier = Courier::create($request->validated());

        return (new CourierResource($courier))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Courier $courier)
    {
        return new CourierResource($courier);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourierRequest $request, Courier $courier)
    {
        $courier->update($request->validated());

        return new CourierResource($courier->fresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Courier $courier)
    {
        $courier->delete();

        return response()->noContent();
    }
}
