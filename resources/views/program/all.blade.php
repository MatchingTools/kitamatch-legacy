@extends('layouts.app')

@section('content')

{{ csrf_field() }}

<script>
  $(document).ready( function () {
    $('#programs').DataTable( {
      "pageLength": 50,
      "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/German.json"
            },
    }
         );
  } );
</script>
<div class="row justify-content-center">
<div class="col-md-8">
<h2>Liste aller Kitagruppen</h2>
</div>
</div>

<div class="row justify-content-center">
<div class="col-md-12  my-3 p-3 bg-white rounded box-shadow">
    <table class="table" id="programs">
      <thead>
        <tr>
            <th>Kita</th>
            <th>Gruppe</th>
            <!--<th>Adresse</th>
            <th>PLZ</th>-->
            <th>Öffentlich / Frei</th>
            <th>Koordinierung</th>
            <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @foreach($programs as $program)
            <tr>
                <td><a href="{{url('/provider/' . $program->proid)}}">{{$program->provider_name}}</a></td>
                <td><a href="{{url('/preference/program/' . $program->pid)}}">{{$program->name}}</a></td>
                <!--<td>{{$program->address}}</td>
                <td>{{$program->plz}}</td>-->
                <td>{{$program->p_kind_description}}</td>
                <td>{{$program->coordination_description}}</td>
                <td>{{$program->status_description}}</td>
            </tr>
        @endforeach
      </tbody>
    </table>
</div>
</div>

@endsection
