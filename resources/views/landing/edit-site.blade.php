@extends('landing.landing')

@section('content')

    <form class="form" action="{{url("/santiye/update-site")}}" method="POST">
        <input type="hidden" name="id" value="{{$site->id}}">
        {!! csrf_field() !!}
    @include('landing._santiye-add-form')
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <button type="submit" class="btn btn-block btn-primary btn-flat">Kaydet</button>
            </div>
        </div>
    </form>

@endsection