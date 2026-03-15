{{-- =========================================================================
    template.blade.php — Le gabarit principal de l'application
    =========================================================================
    Ce fichier est le "squelette" de toutes les pages du site.
    Toutes les autres vues "étendent" ce template avec @extends('template').

    Principe :
    - Ce fichier définit la structure HTML commune à toutes les pages
    (balises <html>, <head>, le header, le footer, les fichiers CSS/JS)
    - Chaque page spécifique remplace les zones @yield() avec son contenu

--}}
<!DOCTYPE html>
<html lang="fr">

<head>
    {{-- Le titre de l'onglet du navigateur.
        @yield('title') sera remplacé par le titre défini dans chaque vue avec @section('title', '...')
        Si aucun titre n'est défini, on utilise 'BookHub - Bibliothèque' par défaut --}}
	<title>@yield('title', 'BookHub - Bibliothèque')</title>

    {{-- Métadonnées : informations sur la page pour le navigateur --}}
	<meta charset="utf-8"> {{-- Encodage des caractères : permet les accents (é, à, ç...) --}}
	<meta http-equiv="X-UA-Compatible" content="IE=edge"> {{-- Compatibilité avec Internet Explorer --}}
	<meta name="viewport" content="width=device-width, initial-scale=1.0"> {{-- Responsive : adapte la page aux mobiles --}}
	<meta name="format-detection" content="telephone=no"> {{-- Empêche iOS de transformer les numéros en liens téléphoniques --}}
	<meta name="apple-mobile-web-app-capable" content="yes"> {{-- Permet l'utilisation en mode app sur iPhone --}}

    {{-- Chargement de Bootstrap 5 : framework CSS qui facilite la mise en page
        Il vient d'un CDN (serveur externe), donc pas besoin de le télécharger --}}
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
		integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">

    {{-- Chargement des fichiers CSS locaux du projet
        asset() génère l'URL correcte vers le dossier public/ --}}
	<link rel="stylesheet" type="text/css" href="{{ asset('css/normalize.css') }}"> {{-- Normalise le style par défaut des navigateurs --}}
	<link rel="stylesheet" type="text/css" href="{{ asset('icomoon/icomoon.css') }}"> {{-- Icônes (user, search, flèches...) --}}
	<link rel="stylesheet" type="text/css" href="{{ asset('css/vendor.css') }}"> {{-- Styles des plugins tiers --}}
	<link rel="stylesheet" type="text/css" href="{{ asset('style.css') }}"> {{-- Style principal du thème BookHub --}}
</head>

{{-- data-bs-spy="scroll" : Bootstrap suit le défilement pour activer le bon élément de menu --}}
<body data-bs-spy="scroll" data-bs-target="#header" tabindex="0">

    {{-- Inclusion du header : équivalent d'un "copier-coller" du fichier header.blade.php --}}
	@include('header')

    {{-- Zone de contenu principal : chaque page remplace cette zone avec @section('content') ... @endsection --}}
	@yield('content')

    {{-- Inclusion du footer --}}
	@include('footer')

    {{-- Chargement des fichiers JavaScript en bas de page (bonne pratique : le HTML se charge d'abord) --}}
	<script src="{{ asset('js/jquery-1.11.0.min.js') }}"></script> {{-- jQuery : bibliothèque JS qui simplifie les interactions --}}
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"
		integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm"
		crossorigin="anonymous"></script> {{-- JavaScript de Bootstrap (menus, modales...) --}}
	<script src="{{ asset('js/plugins.js') }}"></script> {{-- Plugins JavaScript du thème --}}
	<script src="{{ asset('js/script.js') }}"></script> {{-- Script principal du thème --}}

</body>

</html>
