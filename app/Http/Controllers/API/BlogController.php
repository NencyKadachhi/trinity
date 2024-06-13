<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BlogTag;
use App\Models\Category;
use App\Models\UserBlog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function create(Request $request)
    {
        if(!Auth::guard('sanctum')->check()){
           return response()->json(['error' => 'Unauthorized'], 401);
        }
        $data = $request->all();
        try {
            $rules = array(
                'title' => 'required',
                'content' => 'required',
                'slug' => 'required',
                'category' => 'required',
                'tags' => 'required',
                'image' => 'required',
            );
            $messages = array([
                'title' => 'Blog title is required',
                'content' => 'Blog content is required',
                'slug' => 'Slug is required',
                'category' => 'Category is required',
                'category' => 'Tag is required',
                'image' => 'Image is required',
            ]);

            $validator = Validator::make($data, $rules, $messages);

            if ($validator->fails()) {

                $messages = $validator->messages();
                return response()->json([
                    "code" => 401,
                    "message" => $messages,
                ]);
            } else {
                if (isset($data) && count($data) > 0) {
                    $add = new UserBlog();
                    $add->title = $data['title'];
                    $add->content = $data['content'];
                    $add->slug = Str::slug($data['slug']);
                    $add->user_id  = Auth::user()->id;
                    $add->category_id  = $data['category'];
                    $add->is_published  = false;

                    $image = $data['image'];
                    $filename = time() . '.' . $image->getClientOriginalExtension();
                    $add->image = $filename;
                    $request->image->storeAs('images', $filename);

                    if ($add->save()) {
                        $tags = explode(',', $data['tags']);
                        foreach ($tags as $value) {
                            $add_blog_tag = new BlogTag();
                            $add_blog_tag->blog_id = $add->id;
                            $add_blog_tag->tag_id = $value;
                            $add_blog_tag->save();
                        }
                    }
                    return response()->json([
                        "code" => 200,
                        "message" => "Blog Created Successfully!",
                    ]);
                }
            }
        } catch (\Throwable $th) {
            dd($th);
            report($th);
            return false;
        }
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        try {
            $rules = array(
                'title' => 'required',
                'content' => 'required',
                'slug' => 'required',
                'category' => 'required',
                'tags' => 'required',
                'image' => 'required',
            );
            $messages = array([
                'title' => 'Blog title is required',
                'content' => 'Blog content is required',
                'slug' => 'Slug is required',
                'category' => 'Category is required',
                'category' => 'Tag is required',
                'image' => 'Image is required',
            ]);

            $validator = Validator::make($data, $rules, $messages);

            if ($validator->fails()) {

                $messages = $validator->messages();
                return response()->json([
                    "code" => 401,
                    "message" => $messages,
                ]);
            } else {
                $find = UserBlog::where('id', $id)->where('user_id', Auth::user()->id)->first();
                if (!empty($find) && isset($data) && count($data) > 0) {
                    $find->title = $data['title'];
                    $find->content = $data['content'];
                    $find->slug = Str::slug($data['slug']);
                    $find->user_id  = Auth::user()->id;
                    $find->category_id  = $data['category'];
                    $find->is_published  = false;

                    $image = $data['image'];
                    if (isset($image) && !empty($image)) {
                        $filename = time() . '.' . $image->getClientOriginalExtension();
                        $find->image = $filename;
                        $request->image->move(public_path('images'), $filename);
                    }

                    if ($find->save()) {
                        $tags = explode(',', $data['tags']);
                        foreach ($tags as $value) {
                            $add_blog_tag = new BlogTag();
                            $add_blog_tag->blog_id = $id;
                            $add_blog_tag->tag_id = $value;
                            $add_blog_tag->save();
                        }
                    }
                    return response()->json([
                        "code" => 200,
                        "message" => "Blog updated successfully!",
                    ]);
                } else {
                    return response()->json([
                        "code" => 401,
                        "message" => "You cannot edit this blog",
                    ]);
                }
            }
        } catch (\Throwable $th) {
            dd($th);
            report($th);
            return false;
        }
    }
    public function read($id)
    {
        try {
            $find = UserBlog::find($id);
            return response()->json([
                "code" => 200,
                "message" => "Blog updated successfully!",
                "data" => $find
            ]);
        } catch (\Throwable $th) {
            dd($th);
            report($th);
            return false;
        }
    }

    public function delete($id)
    {
        try {
            $find = UserBlog::where('id', $id)->where('user_id', Auth::user()->id)->delete();
            if (!empty($find)) {
                return response()->json([
                    "code" => 200,
                    "message" => "Blog deleted successfully!",
                ]);
            } else {
                return response()->json([
                    "code" => 401,
                    "message" => "You cannot delete this blog",
                ]);
            }
        } catch (\Throwable $th) {
            dd($th);
            report($th);
            return false;
        }
    }

    public function BlogPublished($id)
    {
        try {
            $find = UserBlog::where('id', $id)->where('user_id', Auth::user()->id)->first();
            if (!empty($find)) {
                $find->is_published = true;
                $find->save();
                return response()->json([
                    "code" => 200,
                    "message" => "Blog published successfully!",
                ]);
            } else {
                return response()->json([
                    "code" => 401,
                    "message" => "You cannot published this blog",
                ]);
            }
        } catch (\Throwable $th) {
            dd($th);
            report($th);
            return false;
        }
    }

    public function AllBlogs()
    {
        try {
            $list = UserBlog::where('user_id', '!=', Auth::user()->id)->get();
            if (!empty($list)) {
                return response()->json([
                    "code" => 200,
                    "message" => "Blog list!",
                    "data" => $list
                ]);
            }
        } catch (\Throwable $th) {
            dd($th);
            report($th);
            return false;
        }
    }

    public function list()
    {
        try {
            $list = UserBlog::where('user_id', Auth::user()->id)->get();
            if (count($list) > 0) {
                return response()->json([
                    "code" => 200,
                    "message" => "Blog list!",
                    "data" => $list
                ]);
            } else {
                return response()->json([
                    "code" => 401,
                    "message" => "You have no blogs!",
                    "data" => $list
                ]);
            }
        } catch (\Throwable $th) {
            dd($th);
            report($th);
            return false;
        }
    }

    public function createCategory(Request $request)
    {
        $data = $request->all();
        try {
            $rules = array(
                'category_name' => 'required',
            );
            $messages = array([
                'category_name' => 'Blog category name is required',
            ]);

            $validator = Validator::make($data, $rules, $messages);

            if ($validator->fails()) {

                $messages = $validator->messages();
                return response()->json([
                    "code" => 401,
                    "message" => $messages,
                ]);
            } else {
                if (isset($data) && count($data) > 0) {
                    $add = new Category();
                    $add->category_name = $data['category_name'];
                    if ($add->save()) {
                        return response()->json([
                            "code" => 200,
                            "message" => "Category Created Successfully!",
                        ]);
                    } else {
                        return response()->json([
                            "code" => 401,
                            "message" => "something went wrong!",
                        ]);
                    }
                }
            }
        } catch (\Throwable $th) {
            dd($th);
            report($th);
            return false;
        }
    }
}
