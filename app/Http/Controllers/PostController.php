<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Response;
use App\Models\User;
use App\Models\Post;
use App\Models\Like;
use App\Models\SavedPost;
use App\Models\Comment;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\File;


class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * 
     */
     // this is for upvote counts



    // this is for downvote counts
    function post_data($id)
    {
        $post_data =  Like::where('post_id', $id)
                            ->where('like', '>', '0')
                            ->sum('like');
        return $post_data;
    }
    /// function to get the total of downvotes
    function down_data($id)
    {   
        $down_data = Like::where('post_id', $id)
                            ->where('like', '<', '0')
                            ->count('like');
        return $down_data;
    }

    public function index()
    {
        $user_id = Auth::user()->id;

        $posts = User::join('posts', 'posts.user_id', '=', 'users.id')
                                ->orderBy('posts.created_at', 'desc')
                                ->get();

        $save_posts = SavedPost::where('user_id', $user_id)
                                ->get(['id', 'post_id']);

        // $posts = json_decode($posts);
        // $saved = json_decode($save_posts);

        //dd(gettype($posts));
        foreach($posts as $new)
        {
            foreach($save_posts as $savepost)
            {
                if($new->id == $savepost->post_id)
                {
                    $new->saved = true;
                }
            }
        }
        // dd($posts);

        // //dd($save_post);
        $newsfeed_posts = json_encode($posts);
        //dd(json_decode($newsfeed_posts));
        return view('user.home')->with('newsfeed_posts', json_decode($newsfeed_posts));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user_id = Auth::user()->id;
        $validated = $this->validate($request, [
            'post_title' => ['required', 'min:5'],
            'post_tags' => ['required', 'min:5'],
            'post_content' => ['required', 'min:50'],
            'post_image.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp']
        ]);
        
        if(isset($validated['post_image']))
        {
            // dd($validated['post_image']);
            $created_post = Post::create([
                'unique_id' => Str::random(9),
                'user_id' => $user_id,
                'post_title' => $request->post_title,
                'post_content' => $request->post_content
            ]);
            $images = $request->post_image;
            dd($images);
            foreach($images as $image_name)
            {
                $name = $image_name->getClientOriginalName();
                $image_name->move('images', $name);
                
                PostImage::create([
                    'post_id' => $created_post->id,
                    'image_name' => $image_name,
                ]);
            }
           
            //dd($created_post->id);
        }
        
        //dd($request->all());
        
        
        //$post_title = $request->post_title;

        
        
        return back()->with('success', "Post created Successfully!");

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($unique_id)
    {
        $user_id = Auth::user()->id;
        
        $post = Post::join('users', 'users.id', '=', 'posts.user_id')
                        ->where('posts.unique_id', '=', $unique_id)
                        ->get(['posts.id as post_id', 'posts.*', 'users.*'])
                        ->ToJson();

        $id = Post::where('unique_id', $unique_id)
                    ->pluck('id');

        $saved_posts = SavedPost::where('user_id', $user_id)
                                ->where('post_id', $id[0])
                                ->get(['id', 'post_id']);

        //get the user's liked post
            // => first argument -> id of the post
            // => second argument->id of the user(current logged in user)
        $liked_posts = $this->liked_posts($id[0], $user_id);


        $post_data = $this->post_data($id[0]) - $this->down_data($id[0]);

        return view('user.post', compact('post_data'))
               ->with('post', json_decode($post))
               ->with('saved_posts', $saved_posts)
               ->with('liked_posts', $liked_posts);
    }

    //get the user's liked post
        // => first param -> id of the post
        // => second param->id of the user(current logged in user)

    public function liked_posts($id, $user_id)
    {
        return $liked_posts = Like::where('user_id', $user_id)
                            ->where('post_id', $id)
                            ->get();   
    }

    // 
    //     -> user_id is the user id
    //     -> id is the post_id
    //
    //


    public function vote($post_id, $vote_type)
    {
        /// function to get the sum of upvotes
        function post_data($id)
        {
            $post_data =  Like::where('post_id', $id)
                                ->where('like', '>', '0')
                                ->sum('like');
            return $post_data;
        }
        /// function to get the total of downvotes
        function down_data($id)
        {   
            $down_data = Like::where('post_id', $id)
                                ->where('like', '<', '0')
                                ->count('like');
            return $down_data;
        }
        /// function to get the total vote counts
        function update_vote_value($post_id)
        {
            return post_data($post_id) - down_data($post_id);
        }
        /// function to delete the record 
        function delete($user_id, $post_id)
        {
            return Like::where('user_id', $user_id)->where('post_id', $post_id)->delete();
        }
        function update($like_value, $vote_type)
        {
            $like_value->like = ($vote_type == "upvote") ? 1 : -1;
            $like_value->save();  
            
            return $like_value;
        }
        $user_id = Auth::user()->id;
        //if user has not yet voted into the specific post, it will create a new record
        if(Like::where('user_id', $user_id)->where('post_id', $post_id)->doesntExist())
        {
            Like::create([
                'user_id' => $user_id,
                'post_id' => $post_id,
                'like' => ($vote_type == "upvote") ? 1 : -1
                //shorthand if else, if the user click the upvote($vote_type) it will output 1 otherwise -1
            ]);
            //this calculates the current total vote counts
            $vote_value = update_vote_value($post_id);

            return response()->json([
                'message' => "Success",
                'vote_value' => $vote_value,
                'vote_state' => $vote_type,
                'status' => 200
            ]);
        }
        // if it has, fetch the row and save it to like_value
        $like_value = Like::where('user_id', $user_id)->where('post_id', $post_id)->first();


        if($vote_type == "upvote")
        {
            //if user upvoted and he already upvoted it - it will delete his record on the table hence the vote count will remain to its count before the user voted.
            if($like_value->like > 0)
            {
                delete($user_id, $post_id);
                $vote_state = "default";
            }
            //else if the vote_type is -1 just update the value of like column to positive. [when the user dowvoted it but then click on upvote]
            else 
            {
                update($like_value, $vote_type);
                $vote_state = "altered_to_downvote";
            }
            //this calculates the current total vote counts
            $vote_value = update_vote_value($post_id);

            return response()->json([
                'message' => "Success",
                'vote_value' => $vote_value,
                // 'vote_state' => $vote_state,
                'status' => 200
            ]);
        }
        //if user upvoted and he already downvoted it - it will delete his record on the table hence the vote count will remain to its count before the user voted.
        if($like_value->like < 0)
        {
            delete($user_id, $post_id);
            $vote_state = "default";
        }
        //else if the vote_type is +1 just update the value of like column to negative. [when the user upvoted it but then click on downvote]
        else 
        {
            update($like_value, $vote_type);
            $vote_state = "altered_to_upvote";
        }
            //this calculates the current total vote counts
        $vote_value = update_vote_value($post_id);

        return response()->json([
            'message' => "Success",
            'vote_value' => $vote_value,
            // 'vote_state' => $vote_state,
            'status' => 200
        ]);
    }
    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
       
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'post_title' => ['required', 'min:5'],
            'post_content' => ['required', 'min:50']
        ]);

        $Post_update = Post::find($request->post_id);

        $Post_update->title = $request->post_title;
        $Post_update->post_content = $request->post_content;
        $Post_update->save();   

        return back()->with('success', "Post edited Successfully!");
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

    public function comment(Request $request)
    {
        $user_id = Auth::user()->id;
        $validator = Validator::make($request->all(), [
            'add_comment' => 'required|min:1',
        ]);

        if($validator->fails()) {

            return Response::json([
                'success' => 'false',
                'errors' => $validator->getMessageBag(),
                'status' => 400
            ]);
        }
        
        Comment::create([
            'user_id' => $user_id,
            'post_id' => $request->post_id,
            'comment_content' => $request->add_comment
        ]);

        $comment_data = Comment::join('users', 'users.id', '=', 'comments.user_id')
                                ->where('comments.post_id', $request->post_id)
                                ->orderBy('comments.updated_at', 'desc')
                                ->take(1)
                                ->get(['comments.id as comment_id', 'users.*', 'comments.*']);
                            
        return response()->json([
            'comment_data' =>  $comment_data,
            'status' => 200,
        ]);
    }

    public function comment_onload($user_id)
    {
        $comment_data = Comment::join('users', 'users.id', '=', 'comments.user_id')
                                ->where('post_id', $user_id)
                                ->get(['comments.id as comment_id', 'users.*', 'comments.*']);

        return response()->json([
            'comment_data' =>  $comment_data,
        ]);
    }

    public function save_post($post_id)
    {
        $user_id = Auth::user()->id;
        // $save_post = SavedPost::join('posts', 'posts.id', '=', 'saved_posts.post_id')
        //                         ->where('post_id', $post_id)
        //                         ->get();

        $doesnotexist = SavedPost::where('user_id', $user_id)
                                    ->where('post_id', $post_id)
                                    ->doesntExist();
        if($doesnotexist)
        {
            SavedPost::create([
                'user_id' => $user_id,
                'post_id' => $post_id
            ]);
            return response()->json([
                'message' =>  $doesnotexist,
                'post_id' => $post_id,
                'status' => 200
            ]);
        }

        SavedPost::where('user_id', $user_id)
                    ->where('post_id', $post_id)
                    ->delete();

        return response()->json([
            'message' =>  $doesnotexist,
            'post_id' => $post_id,
            'status' => 200
        ]);
        
    }
}
