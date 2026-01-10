@extends('layouts.app')
@section('content')
@if (session()->has('error'))
    <div class="alert alert-warning">
        {{ session('error') }}
    </div>
@endif
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @livewire('indicadores')
        </div>     
    </div>   
</div>
@endsection