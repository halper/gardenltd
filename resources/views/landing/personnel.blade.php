@extends('landing/landing')

@section('content')

    <h2 class="page-header">
        {{\App\Library\TurkishChar::tr_camel($personnel->name)}}
    </h2>

@stop