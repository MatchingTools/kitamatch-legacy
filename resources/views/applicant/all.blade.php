@extends('layouts.app')

@section('content')

<script>
  $(document).ready( function () {
    $('#applicants').DataTable({
      "pageLength": 50
    });
  } );
</script>

<div class="row justify-content-center">
<div class="col-md-8">
<h2>Liste aller Bewerber</h2>
</div>
</div>

<div class="row justify-content-center">
<div class="col-md-10  my-3 p-3 bg-white rounded box-shadow">

  <a href="{{url('/applicant/add')}}"><button class="btn btn-primary btn-lg btn-block">Bewerber hinzufügen</button></a>

<hr class="mb-4">

    <table class="table" id="applicants">
      <thead>
        <tr>
            <th>ID</th>
            <th>Vorname</th>
            <th>Nachname</th>
            <th>Status</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
        </tr>
      </thead>
        @foreach($applicants as $applicant)
            <tr>
                <td>{{$applicant->aid}}</td>
                <td>{{$applicant->first_name}}</td>
                <td>{{$applicant->last_name}}</td>
                <td>{{$applicant->status}}</td>
                <td><a href="{{url('/applicant/' . $applicant->aid)}}"><button type="button" class="btn btn-primary">Einsehen</button></a></td>
                <td>
                    <form action="{{url('/applicant/' . $applicant->aid)}}" method="POST">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}

                        <button type="button" class="btn btn-light">Löschen</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
</div>

@endsection
