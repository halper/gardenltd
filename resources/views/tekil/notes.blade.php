@extends('tekil.layout')

@section('content')
    <h2>Ertesi Gün Notları</h2>
    <p>Raporlara ait notları görüntüleyebilirsiniz.</p>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Rapor Tarihi</th>
            <th>Not</th>
        </tr>
        </thead>
        <tbody>
        @foreach($site->report()->whereNotNull('notes')->orderBy('created_at', 'DESC')->get() as $rep)
        <tr>
            <td>{{\App\Library\CarbonHelper::getTurkishDate($rep->created_at)}}</td>
            <td>{{$rep->notes}}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
@endsection