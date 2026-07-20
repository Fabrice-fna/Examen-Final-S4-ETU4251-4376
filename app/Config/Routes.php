<?php

use CodeIgniter\Router\RouteCollection;

$routes->get('/', 'Auth::index');
$routes->post('login', 'Auth::attempt');
$routes->get('logout', 'Auth::logout');

$routes->get('client', 'Client::index');
$routes->get('client/depot', 'Client::depot');
$routes->post('client/depot', 'Client::depotValider');
$routes->get('client/retrait', 'Client::retrait');
$routes->post('client/retrait', 'Client::retraitValider');
$routes->get('client/transfert', 'Client::transfert');
$routes->post('client/transfert', 'Client::transfertValider');
$routes->get('client/historique', 'Client::historique');

$routes->get('admin', 'Admin::login');
$routes->post('admin/login', 'Admin::attempt');
$routes->get('admin/logout', 'Admin::logout');
$routes->get('admin/dashboard', 'Admin::dashboard');

$routes->get('admin/prefixes', 'Admin::prefixes');
$routes->post('admin/prefixes/ajouter', 'Admin::prefixeAjouter');
$routes->get('admin/prefixes/supprimer/(:num)', 'Admin::prefixeSupprimer/$1');
$routes->get('admin/prefixes/toggle/(:num)', 'Admin::prefixeToggle/$1');

$routes->get('admin/baremes', 'Admin::baremes');
$routes->post('admin/baremes/ajouter', 'Admin::baremeAjouter');
$routes->get('admin/baremes/supprimer/(:num)', 'Admin::baremeSupprimer/$1');

$routes->get('admin/gains', 'Admin::gains');
$routes->get('admin/frais', 'Admin::frais');
$routes->get('admin/montants-operateurs', 'Admin::montantsParOperateur');
$routes->get('admin/commission-propre', 'Admin::commissionPropre');
$routes->post('admin/commission-propre/enregistrer', 'Admin::commissionPropreEnregistrer');
$routes->get('admin/commission', 'Admin::commission');
$routes->post('admin/commission/enregistrer', 'Admin::commissionEnregistrer');
$routes->get('admin/comptes', 'Admin::comptes');
