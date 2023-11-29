<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $user;
    public $mailView;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $mail_view)
    {
        $this->user     = $user;
        $this->mailView = $mail_view;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Mail::to($this->user->email)->send($this->mailView);

        } catch (\Exception $exception){
            Log::error($exception->getMessage());
        }
    }
}
