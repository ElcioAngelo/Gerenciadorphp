<?php 
      use Carbon\Carbon;
                    Carbon::setLocale('pt_BR');

                    $data = Carbon::now()->translatedFormat('d \d\e F \d\e Y');
                    
    $usuario = 'teste';
?>
@extends('layouts.main')

{{-- Título da página --}}
@section('title', 'Computadores')

{{-- Conteúdo principal da página --}}
@section('content')
        <div class="container mt-5">
            <h1> Bem vindo,{{$usuario}}</h1>
            <h2>{{$data}}</h2>
        </div>

        {{-- TODO: PRECISO CRIAR A CONEXÃO COM O BANCO DE DADOS, E O HTML PARA RENDERIZAR. --}}

@endsection