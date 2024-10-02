<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\TestimonialResource;
use App\Models\Testimonial;
use App\Traits\Common;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    use Common;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $testimonials=Testimonial::all();
        return response()->json([
            "file_path"=>"assets/admin/images/testimonials",
            "testimonials" => TestimonialResource::collection($testimonials),
        ], 200);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'content' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $data['published']=isset($request->published);
        $data['image']=$this->upload_file($request->image,'assets/admin/images/testimonials');
        $testimonial=Testimonial::create($data);
        return response()->json([
            "file_path"=>"assets/admin/images/testimonials",
            "success" => "Testimonial was added successfully",
            "testimonial"=>new TestimonialResource($testimonial),
        ], 200);
    }

    public function show(string $id)
    {
        $testimonial=Testimonial::findOrFail($id);
        return response()->json([
            "testimonial"=>new TestimonialResource($testimonial),
        ], 200);
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'old_image' => 'required|string',
        ]);
        $data['published']=$request->published;
        $data['image']=(isset($request->image))?$this->upload_file($request->image,'assets/admin/images/testimonials'):$request->old_image;
        // dd($data);
        $testimonial->update($data);
        return response()->json([
            "file_path"=>"assets/admin/images/testimonials",
            "success" => "Testimonial was udated successfully",
            "testimonial"=>new TestimonialResource($testimonial),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Testimonial $testimonial)
    {
        $testimonial->delete();
        return response()->json([
            "success" => "Testimonial was deleted successfully",
        ], 200);    }
}
