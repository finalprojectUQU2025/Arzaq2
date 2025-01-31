<?php

namespace App\Http\Controllers;

use App\Models\Condition;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ConditionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Condition::all();
        return response()->view('cms.Conditions.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response()->view('cms.Conditions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator($request->all(), [
            'title' => 'required|string|min:3|max:255',
            'sub_title' => 'required|string|min:3',
        ]);
        if (!$validator->fails()) {
            $condition = new Condition();
            $condition->title = $request->input('title');
            $condition->sub_title = $request->input('sub_title');
            $isSaved = $condition->save();
            return Response()->json(
                ['message' => $isSaved ? 'تم إنشاء الشرط بنجاح.' : 'فشل في الإنشاء!'],
                $isSaved ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST
            );
        } else {
            return response()->json(["message" => $validator->getMessageBag()->first()], Response::HTTP_BAD_REQUEST);
        };
    }

    /**
     * Display the specified resource.
     */
    public function show(Condition $condition)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Condition $condition)
    {
        //
        return response()->view('cms.Conditions.edit', ['condition' => $condition]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Condition $condition)
    {
        $validator = Validator($request->all(), [
            'title' => 'required|string|min:3|max:255',
            'sub_title' => 'required|string|min:3',
            'active' => 'required|boolean',
        ]);
        if (!$validator->fails()) {
            $condition->title = $request->input('title');
            $condition->sub_title = $request->input('sub_title');
            $condition->active = $request->input('active');
            $isSaved = $condition->save();
            return Response()->json(
                ['message' => $isSaved ? 'تم تحديث الشرط بنجاح.' : 'فشل التحديث!'],
                $isSaved ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST
            );
        } else {
            return response()->json(["message" => $validator->getMessageBag()->first()], Response::HTTP_BAD_REQUEST);
        };
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Condition $condition)
    {
        $isDelete = $condition->delete();
        return response()->json([
            'message' => $isDelete ? 'تم الحذف بنجاح' : 'حدث خطأ أثناء الحذف.'
        ], $isDelete ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
    }
}
