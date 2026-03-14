<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\RepresentationRepository;
use App\Repositories\ProgrammationRepository;

class HomeController extends BaseController
{
    public function index(): void
    {
        $representationRepo = new RepresentationRepository($this->pdo);
        $programmationRepo  = new ProgrammationRepository($this->pdo);

        $upcomingRepresentations = $representationRepo->findUpcoming();
        $activeProgrammations    = $programmationRepo->findActive();

        $this->render('public/home', [
            'upcomingRepresentations' => $upcomingRepresentations,
            'activeProgrammations'    => $activeProgrammations,
            'pageTitle'               => 'Accueil',
        ]);
    }
}
