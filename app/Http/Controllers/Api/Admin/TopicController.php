<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\TopicResource;
use App\Models\Category;
use App\Models\Topic;
use App\Traits\Common;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    use Common;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $topics=Topic::with('category')->get();
        return response()->json([
            "topics" => TopicResource::collection($topics),
        ], 200);
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'category_id' => 'required|integer|exists:categories,id',
        ]);
        $data['views']=0;
        $data['published']=isset($request->published);
        $data['trending']=isset($request->trending);
        $data['image']=$this->upload_file($request->image,'assets/admin/images/topics');
        // dd($data);
        $topic=Topic::create($data);
        return response()->json([
            "success" => "topic was added successfully!",
            "topic"=>new TopicResource($topic),
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $topic=Topic::with('category')->findOrFail($id);
        return response()->json([
            "topic"=>new TopicResource($topic),
        ], 200);
    }



    public function update(Request $request, Topic $topic)
    {
        // dd($request->all());
        $data = $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'category_id' => 'required|integer|exists:categories,id',
            'old_image' => 'required|string',
        ]);
        $data['published']=$request->published;
        $data['trending']=$request->trending;
        $data['image']=(isset($request->image)) ? $this->upload_file($request->image,'assets/admin/images/topics'):$request->old_image;
        // dd($data);
        $topic->update($data);
        return response()->json([
            "success" => "topic was Updated successfully!",
            "topic"=>new TopicResource($topic),
        ], 200);    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Topic $topic)
    {
        $topic->delete();
        return response()->json([
            "success" => "topic was deleted successfully!",
        ], 200);
    }
}
