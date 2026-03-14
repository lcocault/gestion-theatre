<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Repositories\RepresentationRepository;
use App\Repositories\ProgrammationRepository;

class DashboardController extends AdminBaseController
{
    public function index(): void
    {
        $this->requireAuth();

        $representationRepo = new RepresentationRepository($this->pdo);
        $programmationRepo  = new ProgrammationRepository($this->pdo);

        $upcoming           = $representationRepo->findUpcoming();
        $activeProgrammations = $programmationRepo->findActive();

        $this->renderAdmin('admin/dashboard', [
            'upcoming'            => $upcoming,
            'activeProgrammations' => $activeProgrammations,
            'pageTitle'           => 'Tableau de bord',
        ]);
    }
}
