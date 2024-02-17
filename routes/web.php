<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Http\Controllers\Profile\AvatarController;
use OpenAI\Laravel\Facades\OpenAI;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\TicketController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
    
    // By using raw query

    // fetch all users

    // $users = DB:: select("select * from users where email=?" , ['hirushamalith558@gmail.com']);
    // $users = DB:: select("select * from users");
    
    // create new users

    // $user = DB:: insert('insert into users (name, email, password) values (?, ?, ?)', [
    //     'Hirusha', 
    //     'hirushamalith560@gmail.com',
    //     'password',
    // ]);
    // dd($user);

    // update a user

    // $user = DB:: update("update users set email=? where id=?" ,[
    // 'malithhirusha424@gmail.com',
    // 3, 
    // ]);

    // delete a user

        // $user = DB:: delete("delete from users where id=3");

    // By using query builder

        // fetch all users by using query builder

        // $users = DB:: table('users')->get();

        // get first user from collection
        // Retrieving a Single Row / Column From a Table

        // $users = DB:: table('users')->where('id', 1)->first();

        // $users = DB:: table('users')->find(1);

        // $users = DB:: table('users')->where('id', 1)->get();

        // insert statement

        // $user = DB::table('users')->insert([
        //     'name' => 'Meedum',
        //     'email' => 'meedum@example.com',
        //     'password' => 'password',
        // ]);

        // update user

        // $user = DB:: table('users')->where('id', 4)->update([
        //     'name' => 'Meedum Vishwa',
        //     'email' => 'meedum@123example.com',
        // ]);

        // delete user

        // $user = DB:: table('users')->where('id', 4)->delete();

    // By using Eloquent models
        
        // fetch all users

        // $users = User::where('id', 1)->first();
        // $users = User::all();
        // $user = User::find(13);

        // create new users

        // $user = User::create([
        //     'name' => 'mahi',
        //     'email' => 'mahinda@example.com',
        //     'password' => 'password',
        //     'password' => bcrypt('password'),
        // ]);

        // update a user
        
        // $user = User::find(5);
        // $user = User::where ('id', 5)->first();
        // $user->update([
        //     'email' => 'muthu@example.com',
        // ]);

        // delete a user
        // $user = User::find(5);
        // $user->delete();


    // dd($user->name);
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/avatar',[AvatarController::class,'update'])->name('profile.avatar');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Route::get('/openai', function(){
    // $result = OpenAI::chat()->create([
    //     'model' => 'gpt-3.5-turbo',
    //     'messages' => [
    //         ['role' => 'user', 'content' => 'Hello!'],
    //     ],
    // ]);

    // $result = OpenAI::images()->create([
    //     "prompt"=> "A cute baby sea otter",
    //     "n"=> 2,
    //     "size"=> "512x512",
    // ]);

    // echo $result->choices[0]->message->content; // Hello! How can I assist you today?

//     dd($result);
// });


// Authentication Routing = Socialite
// Authentication and Storage

Route::post('/auth/redirect', function () {
    return Socialite::driver('github')->redirect();
})->name('login.github');

Route::get('/auth/callback', function () {
    $user = Socialite::driver('github')->user();
    $user = User::firstOrCreate(
        ['email'=> $user->email],[
            'name' => $user->name,
            'password' => 'password',
        
        ]);

        Auth::login($user);
        return redirect('/dashboard');

    // dd($user->email);
    // $user->token
});

// creating a new route for our ticketing system

Route::middleware('auth')->group(function () {
    // Resource route since we have resource controllers then everything gonna directly connected 
    Route::resource('/ticket', TicketController::class);
    // Route::get('/ticket/create', [TicketController::class,'create'])->name('ticket.create');
    // Route::post('/ticket/create', [TicketController::class,'store'])->name('ticket.store');
});