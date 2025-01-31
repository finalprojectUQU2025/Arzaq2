<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $data = Country::all();
        return response()->view('cms.Countries.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return response()->view('cms.Countries.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator($request->all(), [
            'name' => 'required|string|unique:countries|min:3',
            // 'image' => 'required|image|max:2048|mimes:jpg,png',
        ]);

        if (!$validator->fails()) {
            $country = new Country();
            $country->name = $request->input('name');
            if ($request->hasFile('image')) {
                $imageName = time() . '_' . str_replace(' ', '', $country->name) . '.' . $request->file('image')->extension();
                $request->file('image')->storePubliclyAs('Country', $imageName, ['disk' => 'public']);
                $country->image = 'Country/' . $imageName;
            }
            $isSaved = $country->save();
            return Response()->json(
                ['message' => $isSaved ? 'تم إنشاء المدينة بنجاح' : 'فشل في عملية الانشاء!'],
                $isSaved ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST
            );
        } else {
            return response()->json(["message" => $validator->getMessageBag()->first()], Response::HTTP_BAD_REQUEST);
        };
    }

    /**
     * Display the specified resource.
     */
    public function show(Country $country)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Country $country)
    {
        //
        return response()->view('cms.Countries.edit', ['country' => $country]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Country $country)
    {
        //
        $validator = Validator($request->all(), [
            'name' => 'required|string|unique:countries,name,' . $country->id . '|min:3',
            'image' => 'nullable|image|max:2048|mimes:jpg,png',
            'active' => 'required|boolean',
        ]);

        if (!$validator->fails()) {
            $country->name = $request->input('name');
            $country->active = $request->input('active');
            if ($request->hasFile('image')) {
                if ($country->image !== Null) {
                    Storage::disk('public')->delete($country->image);
                }
                $imageName = time() . '_' . str_replace(' ', '', $country->name) . '.' . $request->file('image')->extension();
                $request->file('image')->storePubliclyAs('Country', $imageName, ['disk' => 'public']);
                $country->image = 'Country/' . $imageName;
            }
            $isSaved = $country->save();
            return Response()->json(
                ['message' => $isSaved ? 'تم تعديل المدينة بنجاح' : 'فشل في عملية التعديل!'],
                $isSaved ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST
            );
        } else {
            return response()->json(["message" => $validator->getMessageBag()->first()], Response::HTTP_BAD_REQUEST);
        };
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Country $country)
    {
        //

        $isDelete = $country->delete();
        if ($isDelete) {
            if ($country->image !== Null) {
                Storage::disk('public')->delete($country->image);
            }
        }
        return response()->json([
            'message' => $isDelete ? 'تم الحذف بنجاح' : 'حدث خطأ أثناء الحذف'
        ], $isDelete ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
    }
}
