@extends('layouts.app')

@section('content')

<script>
  $(document).ready( function () {
    $('#guardians').DataTable({
      "pageLength": 50
    });
  } );
</script>

<div class="row justify-content-center">
<div class="col-md-8">
<h2>Liste aller Erziehungsberechtigten</h2>
</div>
</div>

<div class="row justify-content-center">
<div class="col-md-12  my-3 p-3 bg-white rounded box-shadow">
    <table class="table" id="guardians">
        <thead>
        <tr>
            <th>ID</th>
            <th>Nachname</th>
            <th>Vorname</th>
            <th>Status</th>
            <th>PLZ</th>
            <th>Telefonnummer</th>
        </tr>
      </thead>
        <tbody>
        @foreach($guardians as $guardian)
            <tr
                @if ($guardian->status == 52)
                    class="table-valid"
                @endif
                >
                <th>{{$guardian->gid}}</a></th>
                <td><a href="{{url('/guardian/' . $guardian->gid)}}">{{$guardian->last_name}}</a></td>
                <td>{{$guardian->first_name}}</td>
                <td>{{$guardian->status_description}}</td>
                <td>{{$guardian->plz}}</td>
                <td>{{$guardian->phone}}</td>
            </tr>
        @endforeach
      </tbody>
    </table>
</div>
</div>

@endsection
