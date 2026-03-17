<?php
/*
 * This file is part of the KitaMatch app.
 *
 * (c) Sven Giegerich <sven.giegerich@mailbox.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
 /*
 |--------------------------------------------------------------------------
 | Program Match Mail
 |--------------------------------------------------------------------------
 */

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Guardian;
use App\Applicant;
use App\Match;

/**
* This mail class handles with a successfull program match.
*/
class ProgramMatchMail extends Mailable
{
  use Queueable, SerializesModels;

  public $match;

  /**
  * Create a new message instance.
  *
  * @return void
  */
  public function __construct(Match $guardian) {
    $this->match = $match;
  }

  /**
  * Build the message.
  *
  * @return $this
  */
  public function build() {
    return $this->markdown('email.match.program');
  }
}
