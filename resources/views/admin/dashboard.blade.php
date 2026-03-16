@extends('layouts.app')

@section('content')

<script>
  $(document).ready( function () {
    $('#matches').DataTable( {
      "pageLength": 100,
      "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/German.json"
            },
    } );
    $('#no-match').DataTable( {
      "pageLength": 25,
      "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/German.json"
            },
    } );
  } );
</script>

<div class="row justify-content-center">
    <div class="col-md-8">
      <h2>Übersicht <small class="text-muted">Administration</small></h2>
    </div>
</div>

<div class="card-deck mb-3 text-center">
        <div class="card mb-4 box-shadow">
          <div class="card-header">
            <h4 class="my-0 font-weight-normal">Platzvergabe</h4>
          </div>
          <div class="card-body">
            <h1 class="card-title pricing-card-title">{{count($matches)}} / {{$data['totalCapacity']}}</h1>
            <ul class="list-unstyled mt-3 mb-4">
              <li>Zuordnungen</li>
              <li>Verfügbare Plätze</li>
            </ul>
            <a href="#matches"><button type="button" class="btn btn-lg btn-block btn-outline-primary">Zuteilung</button></a>
          </div>
        </div>
        <div class="card mb-4 box-shadow">
          <div class="card-header">
            <h4 class="my-0 font-weight-normal">Bewerber</h4>
          </div>
          <div class="card-body">
            <h1 class="card-title pricing-card-title">{{$data['applicantsVerified']}}
              / {{count($data['applicants'])}}
            </h1>
            <ul class="list-unstyled mt-3 mb-4">
              <li>Bestätigte Bewerber</li>
              <li>Registrierte Bewerber</li>
            </ul>
            <a href="{{url('/applicant/all')}}"><button type="button" class="btn btn-lg btn-block btn-outline-primary">Bewerber</button></a>
          </div>
        </div>
        <div class="card mb-4 box-shadow">
          <div class="card-header">
            <h4 class="my-0 font-weight-normal">Kitas</h4>
          </div>
          <div class="card-body">
            <h1 class="card-title pricing-card-title">{{$data['programsCount']}} & {{$data['providersCount']}}</h1>
            <ul class="list-unstyled mt-3 mb-4">
              <li>Teilnehmende Gruppen</li>
              <li>Registrierte Kitas</li>
            </ul>
            <a href="{{url('/program/all')}}"><button type="button" class="btn btn-lg btn-block btn-outline-primary"> Kitagruppen</button></a>
          </div>
        </div>
      </div>

<div class="row justify-content-center">
  <div class="col-md-6">
          <br>
          @if (!$data['isSet'])
          <a target="_blank" href="{{url('/preference/set')}}"><button class="btn btn-primary btn-lg btn-block">Ranglisten einrasten</button></a>
          @else
          <button class="btn btn-light btn-lg btn-block" disabled>Ranglisten eingerastet</button>
          @endif
        </div>
      </div>

<div class="row justify-content-center">
  <div class="col-md-6">
    <br>
    <a target="_blank" href="{{url('/matching/get')}}"><button class="btn btn-primary btn-lg btn-block">Vergabe starten</button></a>
    <br>
    <br>
  </div>
</div>

<div class="row justify-content-center">
  <div class="col-md-8">
    <h4><span class="badge badge-light badge-admin">{{count($matches)}}</span> Zuordnungen, <span class="badge badge-light badge-admin">{{$data['countRounds']}}.</span> Koordinierungsrunde, <a class="btn btn-warning" target="_blank" href="{{url('/admin/exportAssigned')}}">Export</a></h4>
  </div>
</div>

<div class="row justify-content-center">
  <div class="col-md-12 my-3 p-3 bg-white rounded box-shadow">
        <table class="table" id="matches">
            <thead>
                <tr>
                    <th>Kita</th>
                    <th>Beginn</th>
                    <th>Umfang</th>
                    <th>Kitagruppe</th>
                    <th>Bewerber</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($matches as $match)
                    <tr>
                      <td>{{$match->provider_name}}</td>
                      <td>{{$match->start}}</td>
                      <td>{{$match->scope}}</td>
                      <td><a target="_blank" href="{{url('/preference/program/' . $match->pid )}}">{{$match->program_name}}</a></td>
                      <td><a target="_blank" href="{{url('/applicant/' . $match->aid )}}">{{$match->applicant_name}}</a></td>
                      <td>{{$match->status_text}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@if($data['countRounds'] > 1)
<div class="row justify-content-center pt-5">
  <div class="col-md-8">
    <h4><span class="badge badge-light badge-admin">{{$data['applicantsVerified'] - count($matches)}}</span> Nicht zugeordnete Bewerber, <a class="btn btn-warning" target="_blank" href="{{url('/admin/exportUnassigned')}}">Export</a></h4>
  </div>

  <div class="col-md-10 my-3 p-3 bg-white rounded box-shadow">
        <table class="table" id="no-match">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Geburtsdatum</th>
                    @if (config('kitamatch_config.show_gender'))
                    <th>Geschlecht</th>
                    @endif
                </tr>
            </thead>
            <tbody>
              @foreach($data['non-matches'] as $nonMatch)
                    <tr>
                      <td>{{$nonMatch['first_name']}} {{$nonMatch['last_name']}}</td>
                      <td>{{(new Carbon\Carbon($nonMatch['birthday']))->format('d.m.Y')}}</td>
                      @if (config('kitamatch_config.show_gender'))
                      <td>{{$nonMatch['gender']}}</td>
                      @endif
                    </tr>
              @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection
