<?php

declare(strict_types=1);

/**
 * Routeur simple basé sur l'URL et la méthode HTTP.
 *
 * Format des routes :
 *   [METHOD, pattern_regex, [Controller::class, 'method'], [param_types...]]
 *
 * Les groupes de capture dans le pattern sont passés comme arguments
 * au contrôleur dans l'ordre de leur apparition.
 */

use App\Controllers\HomeController;
use App\Controllers\PieceController;
use App\Controllers\LieuController;
use App\Controllers\ProgrammationController;
use App\Controllers\ReservationController;
use App\Controllers\Admin\AuthController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\LieuAdminController;
use App\Controllers\Admin\TroupeAdminController;
use App\Controllers\Admin\PieceAdminController;
use App\Controllers\Admin\RepresentationAdminController;
use App\Controllers\Admin\ProgrammationAdminController;

// Récupération de l'URI et de la méthode HTTP
$requestUri    = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$requestUri    = '/' . trim($requestUri, '/');
$requestMethod = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

// Simulation de DELETE/PUT via POST + _method (si besoin)
if ($requestMethod === 'POST' && isset($_POST['_method'])) {
    $requestMethod = strtoupper($_POST['_method']);
}

// Définition des routes
// Format : ['METHOD', '/pattern_regex/', [ControllerClass, 'method'], ['param_type', ...]]
$routes = [
    // ─── Pages publiques ────────────────────────────────────────────
    ['GET',  '#^/$#',                                [HomeController::class, 'index'],            []],

    ['GET',  '#^/pieces$#',                          [PieceController::class, 'index'],           []],
    ['GET',  '#^/pieces/(\d+)$#',                    [PieceController::class, 'show'],            ['int']],

    ['GET',  '#^/lieux$#',                           [LieuController::class, 'index'],            []],
    ['GET',  '#^/lieux/(\d+)$#',                     [LieuController::class, 'show'],             ['int']],

    ['GET',  '#^/programmations$#',                  [ProgrammationController::class, 'index'],   []],
    ['GET',  '#^/programmations/(\d+)$#',            [ProgrammationController::class, 'show'],    ['int']],

    // Réservations
    ['GET',  '#^/reservation/(\d+)$#',               [ReservationController::class, 'form'],       ['int']],
    ['POST', '#^/reservation/(\d+)/store$#',         [ReservationController::class, 'store'],      ['int']],
    ['GET',  '#^/reservation/confirmation/([^/]+)$#',[ReservationController::class, 'confirmation'],['string']],
    ['GET',  '#^/reservation/annuler/([^/]+)$#',     [ReservationController::class, 'annuler'],    ['string']],
    ['POST', '#^/reservation/annuler/([^/]+)$#',     [ReservationController::class, 'annuler'],    ['string']],

    // ─── Administration ─────────────────────────────────────────────
    ['GET',  '#^/admin$#',                           [DashboardController::class, 'index'],        []],
    ['GET',  '#^/admin/login$#',                     [AuthController::class, 'loginForm'],         []],
    ['POST', '#^/admin/login$#',                     [AuthController::class, 'login'],             []],
    ['GET',  '#^/admin/logout$#',                    [AuthController::class, 'logout'],            []],

    // Admin – Lieux
    ['GET',  '#^/admin/lieux$#',                     [LieuAdminController::class, 'index'],        []],
    ['GET',  '#^/admin/lieux/create$#',              [LieuAdminController::class, 'create'],       []],
    ['POST', '#^/admin/lieux/store$#',               [LieuAdminController::class, 'store'],        []],
    ['GET',  '#^/admin/lieux/(\d+)/edit$#',          [LieuAdminController::class, 'edit'],         ['int']],
    ['POST', '#^/admin/lieux/(\d+)/update$#',        [LieuAdminController::class, 'update'],       ['int']],
    ['POST', '#^/admin/lieux/(\d+)/delete$#',        [LieuAdminController::class, 'delete'],       ['int']],

    // Admin – Troupes
    ['GET',  '#^/admin/troupes$#',                   [TroupeAdminController::class, 'index'],      []],
    ['GET',  '#^/admin/troupes/create$#',            [TroupeAdminController::class, 'create'],     []],
    ['POST', '#^/admin/troupes/store$#',             [TroupeAdminController::class, 'store'],      []],
    ['GET',  '#^/admin/troupes/(\d+)/edit$#',        [TroupeAdminController::class, 'edit'],       ['int']],
    ['POST', '#^/admin/troupes/(\d+)/update$#',      [TroupeAdminController::class, 'update'],     ['int']],
    ['POST', '#^/admin/troupes/(\d+)/delete$#',      [TroupeAdminController::class, 'delete'],     ['int']],

    // Admin – Pièces
    ['GET',  '#^/admin/pieces$#',                    [PieceAdminController::class, 'index'],       []],
    ['GET',  '#^/admin/pieces/create$#',             [PieceAdminController::class, 'create'],      []],
    ['POST', '#^/admin/pieces/store$#',              [PieceAdminController::class, 'store'],       []],
    ['GET',  '#^/admin/pieces/(\d+)/edit$#',         [PieceAdminController::class, 'edit'],        ['int']],
    ['POST', '#^/admin/pieces/(\d+)/update$#',       [PieceAdminController::class, 'update'],      ['int']],
    ['POST', '#^/admin/pieces/(\d+)/delete$#',       [PieceAdminController::class, 'delete'],      ['int']],

    // Admin – Représentations
    ['GET',  '#^/admin/representations$#',           [RepresentationAdminController::class, 'index'],   []],
    ['GET',  '#^/admin/representations/create$#',    [RepresentationAdminController::class, 'create'],  []],
    ['POST', '#^/admin/representations/store$#',     [RepresentationAdminController::class, 'store'],   []],
    ['GET',  '#^/admin/representations/(\d+)/edit$#',[RepresentationAdminController::class, 'edit'],    ['int']],
    ['POST', '#^/admin/representations/(\d+)/update$#',[RepresentationAdminController::class, 'update'],['int']],
    ['POST', '#^/admin/representations/(\d+)/delete$#',[RepresentationAdminController::class, 'delete'],['int']],
    ['GET',  '#^/admin/representations/(\d+)/reservations$#',
             [RepresentationAdminController::class, 'reservations'], ['int']],
    ['POST', '#^/admin/representations/(\d+)/reservations/([^/]+)/confirmer$#',
             [RepresentationAdminController::class, 'confirmerReservation'], ['int', 'string']],
    ['POST', '#^/admin/representations/(\d+)/reservations/([^/]+)/annuler$#',
             [RepresentationAdminController::class, 'annulerReservation'], ['int', 'string']],
    ['POST', '#^/admin/representations/(\d+)/annuler$#',
             [RepresentationAdminController::class, 'annulerRepresentation'], ['int']],

    // Admin – Programmations
    ['GET',  '#^/admin/programmations$#',            [ProgrammationAdminController::class, 'index'],    []],
    ['GET',  '#^/admin/programmations/create$#',     [ProgrammationAdminController::class, 'create'],   []],
    ['POST', '#^/admin/programmations/store$#',      [ProgrammationAdminController::class, 'store'],    []],
    ['GET',  '#^/admin/programmations/(\d+)/edit$#', [ProgrammationAdminController::class, 'edit'],     ['int']],
    ['POST', '#^/admin/programmations/(\d+)/update$#',[ProgrammationAdminController::class, 'update'],  ['int']],
    ['POST', '#^/admin/programmations/(\d+)/delete$#',[ProgrammationAdminController::class, 'delete'],  ['int']],
];

// Résolution de la route
$matched = false;
foreach ($routes as [$method, $pattern, [$controllerClass, $action], $paramTypes]) {
    if ($requestMethod !== $method) {
        continue;
    }
    if (!preg_match($pattern, $requestUri, $matches)) {
        continue;
    }

    $matched = true;

    // Récupération et cast des paramètres capturés
    $params = [];
    for ($i = 1; $i < count($matches); $i++) {
        $type = $paramTypes[$i - 1] ?? 'string';
        $params[] = match ($type) {
            'int'   => (int) $matches[$i],
            default => (string) $matches[$i],
        };
    }

    // Instanciation du contrôleur (injection PDO lazy)
    $pdo = getDatabaseConnection();
    $controller = new $controllerClass($pdo);
    $controller->$action(...$params);
    break;
}

// 404 si aucune route ne correspond
if (!$matched) {
    http_response_code(404);
    $pdo = null;
    try {
        $pdo = getDatabaseConnection();
    } catch (\Throwable) {
        // Pas de DB pour la page 404
    }
    $pageTitle = 'Page introuvable';
    require VIEW_PATH . '/public/404.php';
}
