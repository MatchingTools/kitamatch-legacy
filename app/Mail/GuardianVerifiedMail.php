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
 | Guardian Verified Mail
 |--------------------------------------------------------------------------
 */

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Guardian;

/**
* This mail class handles with the event of a successfull verification of a guardian and his applicants.
*/
class GuardianVerifiedMail extends Mailable
{
  use Queueable, SerializesModels;

  public $guardian;

  /**
  * Create a new message instance.
  *
  * @return void
  */
  public function __construct(Guardian $guardian) {
    $this->guardian = $guardian;
  }

  /**
  * Build the message.
  *
  * @return $this
  */
  public function build() {
    return $this->markdown('email.guardian.verified');
  }
}
