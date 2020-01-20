<!DOCTYPE html>
<html class="wide wow-animation scrollbar-deep-purple thin square" lang="es">
  <head>
    <title>Tatan Express</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">

    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Montserrat:300,400,500,600,700,900">
    <link rel="stylesheet" href="{{ asset('css/frontend/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fonts.css') }}">
    <link rel="stylesheet" href="{{ asset('css/frontend/style.css') }}">
    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.css') }}">
    <link rel="stylesheet" href="{{ asset('css/frontend/toastr.min.css') }} "/>
      

    @yield('estilo')

    <style>

      /* estilos scrollbar */
      .scrollbar-deep-purple::-webkit-scrollbar-track {
			-webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.1);
			background-color: #F5F5F5;
			border-radius: 10px; }

			.scrollbar-deep-purple::-webkit-scrollbar {
			width: 12px;
			background-color: #F5F5F5; }

			.scrollbar-deep-purple::-webkit-scrollbar-thumb {
			border-radius: 10px;
			-webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.1);
			background-color: #0066cc; }

			.square::-webkit-scrollbar-track {
			border-radius: 0 !important; }

			.square::-webkit-scrollbar-thumb {
			border-radius: 0 !important; }

			.thin::-webkit-scrollbar {
			width: 6px; }
    </style>

  </head>

  <body>
