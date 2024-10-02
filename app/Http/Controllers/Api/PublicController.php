<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\TestimonialResource;
use App\Http\Resources\TopicResource;
use App\Jobs\MessageMailJob;
use App\Models\Category;
use App\Models\Message;
use App\Models\Subscripe;
use App\Models\Testimonial;
use App\Models\Topic;
use Illuminate\Http\Request;


class PublicController extends Controller
{
    public function index()
    {
        $topics = Topic::with('category')->where('published', 1)->take(2)->latest()->get();
        $categories = Category::with(['topics' => function ($query) {
            $query->where('published', 1)->latest()->take(3);
        }])->take(5)->get();
        $testimonials = Testimonial::where('published', 1)->get();
        return response()->json([
            'topics' => TopicResource::collection($topics),
            'categories' => CategoryResource::collection($categories),
            'testimonials' => TestimonialResource::collection($testimonials),
        ], 200);
    }
    public function testimonials()
    {
        $testimonials = Testimonial::where('published', 1)->get();
        return response()->json([
            'testimonials' => TestimonialResource::collection($testimonials),
        ], 200);
    }
    public function topicslisting()
    {
        $popular = Topic::with('category')->where('published', 1)->orderBy('views', 'desc')->simplePaginate(3);
        $trending = Topic::with('category')->where('published', 1)->where('trending', 1)->latest()->take(2)->get();
        // dd($trending);
        return response()->json([
            'trending_Topics' => TopicResource::collection($trending),
            'popular_Topiscs' => TopicResource::collection($popular),
        ], 200);
    }
    public function topicsDetail(String $id)
    {
        $topic = Topic::with('category')->where('published', 1)->findOrFail($id);
        return response()->json([
            'topic' => TopicResource::collection($topic),
        ], 200);
    }

    public function sendContactMessage(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'subject' => 'required|string',
            'message' => 'required|string',
        ]);
        $data['isread'] = 0;
        //store in Db
        Message::create($data);
        //send email in job
        // php artisan queue:work
        MessageMailJob::dispatch($data);
        return response()->json([
            "success" => "Your Message was sent successfully !",
        ], 200);
    }
    public function search(Request $request)
    {
        $keyword = $request->keyword;
        // Search for topics based on the category name
        $topics = Topic::whereHas('category', function ($query) use ($keyword) {
            $query->where('category', 'LIKE', '%' . $keyword . '%');
        })->take(2)->get();
        $category = $keyword;
        return response()->json([
            "topics" => TopicResource::collection($topics),
            "category" => CategoryResource::collection($category),
        ], 200);
    }

    public function readTopic(string $id)
    {

        $topic = Topic::where('published', 1)->findOrFail($id);
        $topic->update([
            'views' => $topic->views + 1,
        ]);
        return response()->json([
            "success" => "This topic was successfully read !",
        ], 200);
    }
    public function newsletter(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|unique:subscripes,email',
        ]);
        $data['active'] = 1;
        Subscripe::create($data);
        return response()->json([
            "success" => "Your email was successfully added !",
        ], 200);
        //creating command to send emails for active subscripers
    }
}
