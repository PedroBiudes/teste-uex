<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class GenericMail extends Mailable
{
	use Queueable, SerializesModels;

	public $data;

	public $subject;

	public $template;

	public $attach;

	public function __construct($subject, array $data = [], $template, $attach = null)
	{
		$this->subject = $subject;
		$this->data = $data;
		$this->template = $template;
		$this->attach = $attach;
	}

	public function build()
	{
		if($this->attach == null)
			return $this
				->subject($this->subject)
				->view($this->template, $this->data);
		else
			return $this
				->subject($this->subject)
				->view($this->template, $this->data)
				->attachData($this->attach['Data'], $this->attach['Nome']);
	}
}