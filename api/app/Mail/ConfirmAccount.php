<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Courriel de confirmation de compte.
 *
 * Class ConfirmAccount
 */
class ConfirmAccount extends Mailable
{
    use Queueable;
    use SerializesModels;
    /**
     * Adresse où le courriel sera envoyé.
     *
     * @var string
     */
    protected $email;

    /**
     * Code de confirmation généré, à placer dans une l'URL.
     *
     * @var string
     */
    protected $code;

    /**
     * Nom de l'utilisateur à qui l'on envoit le courriel.
     *
     * @var string
     */
    public $name;

    /**
     * Créer une nouvelle instance du message.
     *
     * @param string $email
     * @param string $code
     * @param string $name
     */
    public function __construct(string $email, string $code, string $name)
    {
        $this->email = $email;
        $this->code = $code;
        $this->name = $name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->to($this->email)
            ->subject('LAN de l\'ADEPT - Confirmation du compte')
            ->view('emails.confirm-account')
            ->with([
                'code' => $this->code,
                'name' => $this->name,
            ]);
    }
}
