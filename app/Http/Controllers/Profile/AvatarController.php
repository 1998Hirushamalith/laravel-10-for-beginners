<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateAvatarRequest;
use Illuminate\Support\Facades\Storage;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Str;


class AvatarController extends Controller
{
    public function update(UpdateAvatarRequest $request)
    {

        // validation inside the controller

        // $request->validate([
        //     'avatar' => ['required' , 'image'],


        // ]);

        // htpp request

        // dd($request->input('_token'));
        // dd($request->input('avatar'));
        // dd($request->all());

        // dd($request->file('avatar'));

        // $request->file('avatar')->store('avatars');

        // $path = $request->file('avatar')->store('avatars','public');

        // here we can store any image or any file by using Facades
        
           $path = Storage::disk('public')->put('avatars', $request->file('avatar'));
        // dd($path);

        // store file or image in our storage directory inside of the laravel + information of the path
        // of the file will get stored on tp the database

        // auth()->user()->update(['avatar' => storage_path('app')."/$path"]);

        // removing old avatar

        if($oldAvatar = $request->user()->avatar){
            // dd($oldAvatar);
            Storage::disk('public')->delete($oldAvatar);
        }

        
        auth()->user()->update(['avatar' => $path]);

        // authenticate user

        // dd(auth()->user());

        // store avatar

        // return response()->redirectTo(route('profile.edit'));

        return redirect(route('profile.edit'))->with('message','Avatar is updated');

        // Redirects

        // return back()->with('message', 'Avatar is changed.');  
    }

    public function generate(Request $request)
    {
        $result = OpenAI::images()->create([
            "prompt" => 'create avatar for user with cool style animated',
            'n'      => 1,
            'size'   => "256x256",
        ]);

        $contents = file_get_contents($result->data[0]->url);

        $filename = Str::random(25);

        if ($oldAvatar = $request->user()->avatar) {
            Storage::disk('public')->delete($oldAvatar);
        }

        Storage::disk('public')->put("avatars/$filename.jpg", $contents);

        auth()->user()->update(['avatar' => "avatars/$filename.jpg"]);
        return redirect(route('profile.edit'))->with('message', 'Avatar is updated');
    }
}
