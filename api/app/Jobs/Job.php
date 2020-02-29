<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

abstract class Job implements ShouldQueue
{
    /*
    |-------------------------------------------------------------------------------------------------------------------
    | Queueable Jobs
    |-------------------------------------------------------------------------------------------------------------------
    |
    | Cette classe de base fournit un emplacement central pour placer toute logique partagée entre tous vos travaux.
    | Le trait fourni avec la classe fournit un accès aux méthodes auxiliaires de la file d'attente "queueOn" et
    | "delay".
    |
    */

    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
}
