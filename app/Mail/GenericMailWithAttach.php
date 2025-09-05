<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class GenericMailWithAttach extends Mailable
{
	use Queueable, SerializesModels;

	public $data;

	public $subject;

	public $template;

	public $attach;

	public function __construct($subject, array $data, $template, $attach = null, $ext = null)
	{
		$this->subject = $subject;
		$this->data = $data;
		$this->template = $template;
		$this->attach = $attach;
		$this->ext = $ext;
	}

	public function build()
	{
        $mail = $this->subject($this->subject)->view($this->template, $this->data);

		if($this->attach != null) {
            foreach($this->attach as $file) {
				$ext = $this->ext != null ? $this->ext : pathinfo($file["URL"], PATHINFO_EXTENSION);
                $mail->attach($file["URL"], [
                    'as' => $file["Nome"],
                    'mime' => 'application/'.$ext,
                ]);
            }
        }

        return $mail;
	}
}