<?php

$supportedLanguages = ['pt', 'en', 'es'];

$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'pt';

if (!in_array($lang, $supportedLanguages, true)) {
    $lang = 'pt';
}

$_SESSION['lang'] = $lang;

$translations = [
    'pt' => [
        'home' => 'Home',
        'projects' => 'Projetos',
        'studio' => 'Estúdio',
        'articles' => 'Artigos',
        'publications' => 'Publicações',
        'videos' => 'Vídeos',
        'contact' => 'Contato',
        'collaborate' => 'Vamos colaborar',
        'explore_projects' => 'Explorar projetos',
        'view_profile' => 'Ver perfil',
    ],
    'en' => [
        'home' => 'Home',
        'projects' => 'Projects',
        'studio' => 'Studio',
        'articles' => 'Articles',
        'publications' => 'Publications',
        'videos' => 'Videos',
        'contact' => 'Contact',
        'collaborate' => "Let's collaborate",
        'explore_projects' => 'Explore projects',
        'view_profile' => 'View profile',
    ],
    'es' => [
        'home' => 'Inicio',
        'projects' => 'Proyectos',
        'studio' => 'Estudio',
        'articles' => 'Artículos',
        'publications' => 'Publicaciones',
        'videos' => 'Videos',
        'contact' => 'Contacto',
        'collaborate' => 'Colaboremos',
        'explore_projects' => 'Explorar proyectos',
        'view_profile' => 'Ver perfil',
    ],
];

$t = $translations[$lang];