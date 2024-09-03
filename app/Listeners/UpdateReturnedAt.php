<?php

namespace App\Listeners;

use App\Events\BookReturned;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateReturnedAt
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }
   
    
    /**
     * Handle the event.
     */
    
    
    public function handle(BookReturned $event)
    {
        $borrowRecord = $event->borrowRecord;
        $borrowRecord->update(['due_date' => now()]);
        
        Log::info('Book returned, returned_at updated for record ID: ' . $borrowRecord->id);
    }
   
}
