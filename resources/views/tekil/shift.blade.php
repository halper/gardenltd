<?php
use App\Library\CarbonHelper;
use Carbon\Carbon;

$today = CarbonHelper::getTurkishDate(Carbon::now()->toDateString());
?>
@extends('tekil.layout')

@section('page-specific-css')
    <link href="<?= URL::to('/'); ?>/css/daterangepicker.css" rel="stylesheet"/>
    <link href="<?= URL::to('/'); ?>/css/select2.min.css" rel="stylesheet"/>
@stop

@section('page-specific-js')
    <script src="<?= URL::to('/'); ?>/js/moment.min.js" type="text/javascript"></script>
    <script src="<?= URL::to('/'); ?>/js/daterangepicker.js" type="text/javascript"></script>
    <script src="<?= URL::to('/'); ?>/js/select2.min.js" type="text/javascript"></script>

    <script>
        $('input[name="daterange"]').daterangepicker({
            locale: {
                format: 'DD.MM.YYYY'
            },
            maxDate: '{{$today}}'
        });
    </script>
@stop

@section('content')
    <input type="text" name="daterange"/>
@stop