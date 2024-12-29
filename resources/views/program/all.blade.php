@extends('layouts.app')

@section('content')

{{ csrf_field() }}
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
  $(document).ready( function () {
    $('#programs').DataTable( {
      "pageLength": 50,
      "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/German.json"
            },
    });
  });

  var pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
      cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}'
    });

  var channel = pusher.subscribe('matching-completed');
    channel.bind('page-reload', function() {
      location.reload();
    });
</script>

<div class="row justify-content-center">
  <h2>Liste aller Kitagruppen <small class="text-muted">(Platzvergabe: {{$programs->totalMatches}}/{{$programs->totalCapacity}})</small></h2>
  <div class="col-md-12  my-3 p-3 bg-white rounded box-shadow">
      <table class="table" id="programs">
        <thead>
          <tr>
              <th>Kita</th>
              <th>Gruppe</th>
              <th>Gesamtkapazität</th>
              <th>Angebote</th>
              <th>Kita fertig</th>
              <th>Öffentlich / Frei</th>
              <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @foreach($programs as $program)
              <tr>
                  <td><a href="{{url('/provider/' . $program->proid)}}">{{$program->provider_name}}</a></td>
                  <td><a href="{{url('/preference/program/' . $program->pid)}}">{{$program->name}}</a></td>
                  <td>{{$program->capacity}}</td>
                  <td>{{$program->total_offer}}</td>
                  <td style="color: {{ $program->process_complete == 'Ja' ? 'green' : 'red' }};">{{$program->process_complete}}</td>
                  <td>{{$program->p_kind_description}}</td>
                  <td>{{$program->status_description}}</td>
              </tr>
          @endforeach
        </tbody>
      </table>
  </div>
</div>

@endsection
