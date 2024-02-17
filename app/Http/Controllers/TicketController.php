<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Notifications\TicketUpdatedNotification;
use App\Models\User;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // we needs to take tickets for particular user only.
        // give me for ttickets user only first , if user is admin then
        // i needs to get all the tickets otherwise give me for user specific tickets
        // instead of all the tickets i want to get latest things then get so
        $user    = auth()->user();
        $tickets = $user->isAdmin ? Ticket::latest()->get() : $user->tickets;
        return view('ticket.index', compact('tickets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('ticket.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketRequest $request)
    {
        $ticket = Ticket::create([
            'title'       => $request->title,
            'description' => $request->description,
            'user_id'     => auth()->id(),
        ]);


        if ($request->file('attachement')) {
            $this->storeAttachment($request, $ticket);
        }

        // return response()->redirect(route('ticket.index'));
        return redirect(route('ticket.index'));
    }

    /**
     * Display the specified resource.
     */

    //  Route model binding - automatically inject the model instances directly into your routes

    public function show(Ticket $ticket)
    {
        // dd($ticket);
        return view('ticket.show', compact('ticket'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket)
    {
        return view('ticket.edit', compact('ticket'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        // $ticket->update(['title' => $request->title, 'description' => $request->description]);
        // dd($request->all());
        $ticket->update($request->except('attachement'));

        if($request->has('status')){
            // only happen when there is a changes happen here - we can get the user from the ticket
            // $user = User::find($ticket->user_id);
            // here we can connect the ticket table and user table in that case we don't have to say
            $ticket->user->notify(new TicketUpdatedNotification($ticket));

            // return (new TicketUpdatedNotification($ticket))->toMail($user);

            
        }

        if($request->file('attachement')) {

            // Check if attachment exists before deleting

            Storage::disk('public')->delete($ticket->attachement);
            $this->storeAttachment($request, $ticket);

        }
   
        return redirect(route('ticket.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        // dd($ticket);
        $ticket->delete();
        return redirect(route('ticket.index'));
    }

    protected function storeAttachment($request, $ticket)
    {

    /**
     * Store the attachment file.
     */
        $ext      = $request->file('attachement')->extension();
        $contents = file_get_contents($request->file('attachement'));
        $filename = Str::random(25);
        $path     = "attachements/$filename.$ext"; // Use path() to get the file contents
        Storage::disk('public')->put($path, $contents);  
        $ticket->update(['attachement' => $path]);
    }
}
