<?php

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'gr_sistema');
define('BASE_URL', 'http://localhost/sistema');

define('SITE_NAME', 'Estoque');
define('SITE_LOGIN', 'Area Administrativa');
define('SITE_AUTHOR', 'GNR System');
define('SITE_DESCRIPTION', 'HelpChat - Soluções de TI em tempo real.');
define('SITE_KEYWORDS', 'HelpChat, Sistema de Chamados, Suporte Técnico, Gestão de TI, Atendimento ao Cliente, Help Desk, Soluções de TI');
define('SITE_LOCALE', 'pt_BR');
define('SITE_CHARSET', 'utf8');


define('BASE', dirname(__DIR__, 2));
define('BASE_LOGIN', BASE_URL . '/login');
define('BASE_IMG', BASE_URL . '/public/images');
define('BASEJS', BASE_URL . '/public/js');
define('APP', dirname(__DIR__));
define('VIEWS', APP . '/views');
define('HOME', APP . '/views/home');
define('LAYOUTS', VIEWS . '/layouts');
define('CONTROLLERS', APP . '/Controllers');
define('MODELS', APP . '/Models');
define('BASE_CONF', APP . '/config');
define('BASE_CORE', APP . '/Core');
define('BASE_HELPERS', APP . '/Helpers');

define('API', APP . '/api');
define('API_URL', BASE_URL . '/app/api');
define('API_URL1', __DIR__ . '/api');

define('EMAIL', '');

define('BASE_REMEMBER', BASE_URL . '/login/remember');
define('BASE_NEWPASS', BASE_URL . '/login/newpass');
