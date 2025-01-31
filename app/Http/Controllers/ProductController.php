<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Product::all();
        return response()->view('cms.Products.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response()->view('cms.Products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator($request->all(), [
            'name' => 'required|string|unique:countries|min:3',
            'image' => 'required|image|max:2048|mimes:jpg,png',
        ]);

        if (!$validator->fails()) {
            $product = new Product();
            $product->name = $request->input('name');
            if ($request->hasFile('image')) {
                $imageName = time() . '_' . str_replace(' ', '', $product->name) . '.' . $request->file('image')->extension();
                $request->file('image')->storePubliclyAs('Product', $imageName, ['disk' => 'public']);
                $product->image = 'Product/' . $imageName;
            }
            $isSaved = $product->save();
            return Response()->json(
                ['message' => $isSaved ? 'تم إنشاء المنتج بنجاح' : 'فشل في عملية الانشاء!'],
                $isSaved ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST
            );
        } else {
            return response()->json(["message" => $validator->getMessageBag()->first()], Response::HTTP_BAD_REQUEST);
        };
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return response()->view('cms.Products.edit', ['product' => $product]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        if (in_array($product->id, [1, 2])) {
            return response()->json([
                'message' => 'لا يمكن تعديل هذا المنتج'
            ], Response::HTTP_BAD_REQUEST);
        } else {
            $validator = Validator($request->all(), [
                'name' => 'required|string|unique:products,name,' . $product->id . '|min:3',
                'image' => 'nullable|image|max:2048|mimes:jpg,png',
                'active' => 'required|boolean',
            ]);

            if (!$validator->fails()) {
                $product->name = $request->input('name');
                $product->active = $request->input('active');
                if ($request->hasFile('image')) {
                    if ($product->image !== Null) {
                        Storage::disk('public')->delete($product->image);
                    }
                    $imageName = time() . '_' . str_replace(' ', '', $product->name) . '.' . $request->file('image')->extension();
                    $request->file('image')->storePubliclyAs('Country', $imageName, ['disk' => 'public']);
                    $product->image = 'Country/' . $imageName;
                }
                $isSaved = $product->save();
                return Response()->json(
                    ['message' => $isSaved ? 'تم تعديل المنتج بنجاح' : 'فشل في عملية التعديل!'],
                    $isSaved ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST
                );
            } else {
                return response()->json(["message" => $validator->getMessageBag()->first()], Response::HTTP_BAD_REQUEST);
            };
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        if (in_array($product->id, [1, 2])) {
            return response()->json([
                'message' => 'لا يمكن حذف هذا المنتج'
            ], Response::HTTP_BAD_REQUEST);
        } else {
            $isDelete = $product->delete();
            if ($isDelete) {
                if ($product->image !== null) {
                    Storage::disk('public')->delete($product->image);
                }
            }
            return response()->json([
                'message' => $isDelete ? 'تم الحذف بنجاح' : 'حدث خطأ أثناء الحذف'
            ], $isDelete ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
        }
    }
}
