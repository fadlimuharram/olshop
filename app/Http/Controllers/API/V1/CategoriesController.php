<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\API\V1\Category as CategoryResource;
use App\Http\Resources\API\V1\CategoryParent as CategoryParentResource;
use App\Http\Resources\API\V1\CategoryOnly as CategoryOnlyResource;
use App\Http\Resources\API\V1\JSend_Fail;
use App\Http\Resources\API\V1\JSend_Success;
use Illuminate\Support\Facades\Validator;
use App\Category;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return new CategoryResource(
            Category::where('title', 'LIKE', '%' . $request->get('q') . '%')
                ->with(['parent'])
                ->orderBy('parent_id', 'asc')
                ->paginate(10)
        );
    }

    public function indexParent(Request $request)
    {
        return new CategoryParentResource(
            collect(
                \DB::select("SELECT * FROM categories WHERE id = parent_id")
            )
        );
    }

    public function indexAll(Request $request)
    {
        return new CategoryOnlyResource(Category::get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255|unique:categories',
            'parent_id' => 'exists:categories,id'
        ]);
        if ($validator->fails()) {
            return new JSend_Fail($validator->messages());
        }

        $categoryInsert = Category::create($request->all());

        if (!$request->parent_id) {
            $category = Category::find($categoryInsert->id);
            $category->parent_id = $categoryInsert->id;
            $category->save();
        }

        return new JSend_Success($categoryInsert);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'title' =>
                'required|string|max:255|unique:categories,title,' .
                    $category->id,
            'parent_id' => 'exists:categories,id'
        ]);
        if ($validator->fails()) {
            return new JSend_Fail($validator->messages());
        }
        $category->update($request->all());
        return new JSend_Success($category);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::find($id);
        if ($category == null) {
            return new JSend_Fail(['message' => 'not found']);
        }
        $category->delete();
        return new JSend_Success($category);
    }
}
