@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>OnlineDent</h1>
@stop

@section('content')

<img src="https://www.teeth22.com/wp-content/uploads/2020/03/absceso-dental-800x399.png" width="100%">

@stop

@section('css')
  <!--  <link rel="stylesheet" href="/css/admin_custom.css"> -->
  <link href="{{asset('css/app.css')}}" rel="stylesheet">
@stop

@section('js')
    <!-- <script> console.log('Hi!'); </script> -->
    <script scr="{{asset('js/app.js')}}"></script>
@stop