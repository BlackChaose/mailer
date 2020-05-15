<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Mailing;

class Mailing1 extends Mailable
{
    use Queueable, SerializesModels;
    private $message;
    private $files ;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($message, $files=[])
    {
        $this->message = $message;
        $this->files = $files;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mes = $this->subject($this->message["subject"])->view('admin.email_1', ['md' => $this->message]);
        foreach ($this->files as $file) {
            $mes->attach($file['path_to_file']);
        }
        return $mes;
    }
}
