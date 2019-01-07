<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\API\V1\Product as ProductResource;
use App\Http\Resources\API\V1\JSend_Fail;
use App\Http\Resources\API\V1\JSend_Success;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Product;
use File;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return new ProductResource(
            Product::where('name', 'LIKE', '%' . $request->get('q') . '%')
                ->with('categories')
                ->paginate(2)
        );
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
            'name' => 'required|unique:products',
            'model' => 'required',
            'photo' => 'mimes:jpeg,png|max:10240',
            'price' => 'required|numeric|min:1000'
        ]);

        if ($validator->fails()) {
            return new JSend_Fail($validator->messages());
        }

        $data = $request->only('name', 'model', 'price');

        if ($request->hasFile('photo')) {
            $data['photo'] = $this->savePhoto($request->file('photo'));
        }

        $product = Product::create($data);
        $category = \App\Category::find($request->get('category_lists'));

        $product->categories()->attach($category);

        return new JSend_Success($product);
    }

    protected function savePhoto(UploadedFile $photo)
    {
        $fileName = str_random(40) . '.' . $photo->guessClientExtension();
        $destinationPath = public_path() . DIRECTORY_SEPARATOR . 'img';
        $photo->move($destinationPath, $fileName);
        return $fileName;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return new ProductResource(
            Product::where('id', $id)
                ->with('categories')
                ->get()
        );
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
        $product = Product::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:products,name,' . $product->id,
            'model' => 'required',
            'photo' => 'mimes:jpeg,png|max:10240',
            'price' => 'required|numeric|min:1000'
        ]);
        if ($validator->fails()) {
            return new JSend_Fail($validator->messages());
        }

        $data = $request->only('name', 'model', 'price');

        if ($request->hasFile('photo')) {
            $data['photo'] = $this->savePhoto($request->file('photo'));
            if ($product->photo != '') {
                $this->deletePhoto($product->photo);
            }
        }

        $product->update($data);
        if (count($request->get('category_lists')) > 0) {
            $product->categories()->sync($request->get('category_lists'));
        } else {
            $product->categories()->detach();
        }

        return new JSend_Success($product);
    }

    private function deletePhoto($filename)
    {
        $path =
            public_path() .
            DIRECTORY_SEPARATOR .
            'img' .
            DIRECTORY_SEPARATOR .
            $fileName;
        return File::delete($path);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
