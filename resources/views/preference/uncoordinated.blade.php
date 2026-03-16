@extends('layouts.app')

@section('content')



<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

<script>
  $(document).ready( function () {
    $('#offers').DataTable( {
      "pageLength": 100,
      "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/German.json"
            },
    } );

    $('#form_offer').on('submit',function(){
      $('#btn_submit_offer').attr('disabled','true');
    })
  } );
</script>

<div class="panel-body">

  <div class="row justify-content-center">
    <div class="col-md-8">
      <h2>{{$program->name}} | {{$program->provider_name}} <small class="text-muted">Kitagruppe</small></h2>

      <h5>Koordinierungsrunde: <span class="badge badge-light">{{$round}}</span> (<a href="{{url('/preference/program/' . $program->pid)}}">aktualisieren</a>)</h5>

      <table class="table table-bordered">
        <thead>
          <tr>
            <th scope="col">Beginn</th>
            <th scope="col">Umfang</th>
            <th scope="col">Angebote</th>
            <th scope="col">Freie Plätze</th>
            <th scope="col">Bewerber</th>
          </tr>
        </thead>
        <tbody>

@foreach (config('kitamatch_config.care_starts') as $key_start => $start)
@if ($key_start != -1)
@foreach (config('kitamatch_config.care_scopes') as $key_scope => $scope)
@if ($key_scope != -1)
@if ($capacities->where('care_start', '=', $key_start)->where('care_scope', '=', $key_scope)->first()->capacity > 0)
<tr>
     <td>{{$start}}</td>
     <td>{{$scope}}</td>
     <td>{{$program->openOffers[$key_start][$key_scope]}}</td>
     <td>{{$capacities->where('care_start', '=', $key_start)->where('care_scope', '=', $key_scope)->first()->capacity}}</td>
     <td>{{$countApplicants[$key_start][$key_scope]}}</td>
   </tr>
@endif
@endif
@endforeach
@endif
@endforeach
</tbody>
</table>

      @if (count($availableApplicants) == 0)
      <div class="alert alert-warning" role="alert">
        Aktuell sind noch keine Bewerber verfügbar.
      </div>
      @endif

      <!--
      <br>
      <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>Die versendeten Angebote entsprechen ihrer maximalen Anzahl an Plätzen.</strong> Sie können nun bis zur nächsten Koordinierungsrunde keine weiteren Angebote mehr unterbreiten. Bitte aktualieren Sie diese Seite sobald die aktuelle Runde beendet ist.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    -->
    </div>
  </div>

  <div class="row justify-content-center">

    <div class="col-md-12 my-3 p-3 bg-white rounded box-shadow">

    <h4>Verbindliche Angebote <small class="text-muted" style="float: right;"><span class="badge badge-info">Gehaltenes Angebot</span>
<span class="badge badge-success">Endgültige Zusage</span></small></h4>

    <table class="table" id="offers">
      <thead>
          <tr>
              <th>ID</th>
              <th>Vornamen</th>
              <th>Nachnamen</th>
              <th>Geburtsdatum</th>
              @if (config('kitamatch_config.show_gender'))
              <th>Geschlecht</th>
              @endif
              <th>Beginn</th>
              <th>Umfang</th>
              <th>&nbsp;</th>
          </tr>
      </thead>
      <tbody>
        @if (count($preferences) > 0)
          @foreach ($preferences as $preference)
            @if ($preference->status != -1)
              <?php $applicant = $availableApplicants->where('aid', '=', $preference->id_to)->first(); ?>
              @if ($applicant->status == 26)
                <tr class="table-success">
                  <th scope="row">{{$applicant->aid}}</th>
                  <td>{{$applicant->first_name}}</td>
                  <td>{{$applicant->last_name}}</td>
                  <td>{{(new Carbon\Carbon($applicant->birthday))->format('d.m.Y')}}</td>
                  @if (config('kitamatch_config.show_gender'))
                  <td>{{$applicant->gender}}</td>
                  @endif
                  <td>{{config('kitamatch_config.care_starts')[$preference->start]}}</td>
                  <td>{{config('kitamatch_config.care_scopes')[$preference->scope]}}</td>
                  <td><span class="badge badge-success">Endgültige Zusage</span></td>
                </tr>
              @endif
            @endif
          @endforeach
          @foreach ($preferences as $preference)
            @if ($preference->status != -1)
              <?php $applicant = $availableApplicants->where('aid', '=', $preference->id_to)->first(); ?>
              @if ($applicant->status != 26)
                <tr class="table-info">
                  <th scope="row">{{$applicant->aid}}</th>
                  <td>{{$applicant->first_name}}</td>
                  <td>{{$applicant->last_name}}</td>
                  <td>{{(new Carbon\Carbon($applicant->birthday))->format('d.m.Y')}}</td>
                  @if (config('kitamatch_config.show_gender'))
                  <td>{{$applicant->gender}}</td>
                  @endif
                  <td>{{config('kitamatch_config.care_starts')[$preference->start]}}</td>
                  <td>{{config('kitamatch_config.care_scopes')[$preference->scope]}}</td>
                  <td>
                    @if ($preference->updated_at >= $lastMatch)
                      <form action="{{url('/preference/program/uncoordinated/' . $preference->prid)}}"
                        id="delete_{{$preference->prid}}" name="delete_{{$preference->prid}}" method="POST">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}
                        <button form="delete_{{$preference->prid}}" type="submit" class="badge badge-light">Zurücknehmen</button>
                      </form>
                    @else
                      <span class="badge badge-info">Gehaltenes Angebot</span>
                    @endif
                  </td>
                </tr>
              @endif
            @endif
          @endforeach
        @endif
      </tbody>
    </table>

</div></div>

<!--
<div class="row justify-content-center">
  <div class="col-md-12 my-3 p-3 bg-white rounded box-shadow">

    <h4>Warteliste</h4>

    <table class="table" id="waitlist">
      <thead>
          <tr>
              <th>ID</th>
              <th>Vornamen</th>
              <th>Nachnamen</th>
              <th>Geburtsdatum</th>
              <th>Geschlecht</th>
              <th>&nbsp;</th>
              <th>&nbsp;</th>
          </tr>
      </thead>
      <tbody id="sortable">
        @if (count($offers) > 0)
        <script>
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $("input[name=_token]").val()
                }
            });

            $(function() {
              $('#sortable').sortable({
                axis: 'y',
                update: function (event, ui) {
                  $("span.rank").text(function() {
                    return $(this).parent().index("tr")+1;
                  });
                  var order = $(this).sortable('serialize');
                  var _token = $("input[name=_token]").val();
                  var data = {"order": order, "_token": _token};
                  $.ajax({
                    data: data,
                    type: 'POST',
                    url: '{{url('/preference/program/uncoordinated/reorder/' . $preferences[0]->id_from)}}',
                    success: function(data) {
                      console.log(data);
                    }
                  });
                }
              })
              $( "#sortable" ).disableSelection();
            });
        </script>

        {{ csrf_field() }}
        @foreach($offers as $offer)
          @if ($offer['status'] != -1 &&
           $offer['rank'] > 1 &&
           $availableApplicants->where('aid', '=', $offer['id_to'])->first()->status != 26
          )
          <tr id="item-<?php
            $applicant = $availableApplicants->where('aid', '=', $offer['id_to'])->first();
            $key = array_search($applicant->aid, array_column($preferences, 'id_to'));
            echo $preferences[$key]->prid;
            ?>">
            <th scope="row"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span></th>
            <td>{{$applicant->first_name}} (<a target="_blank"  href="/preference/applicant/{{$applicant->aid}}">{{$applicant->aid}}</a>)</td>
            <td>{{$applicant->last_name}}</td>
            <td>{{(new Carbon\Carbon($applicant->birthday))->format('d.m.Y')}}</td>
            <td>{{$applicant->gender}}</td>
            <td>
                @if ($program->openOffers < $program->capacity)
                <form action="{{url('/preference/program/uncoordinated/upoffer')}}" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" name="prid" value="{{$offers[$applicant->aid]['id']}}">
                    <button>Angebot</button>
                </form>
                @else
                  <button disabled>Angebot</button>
                @endif
              </td><td>
                @if ($offers[$applicant->aid]['id'] > 0
                  && $applicant->status != 26)
                  <form action="{{url('/preference/program/uncoordinated/' . $offers[$applicant->aid]['id'])}}" method="POST">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <button>Zurücknehmen</button>
                  </form>
                @endif
            </td>
          </tr>
          @endif
        @endforeach
        @endif
      </tbody>
    </table>

</div></div>
-->

<div class="row justify-content-center">
  <div class="col-md-12 my-3 p-3 bg-white rounded box-shadow">

    <h4>Bewerberliste
      <small class="text-muted" style="float: right;">
        <span class="badge badge-light">Verfügbarer Bewerber</span>
        <!--<span class="badge badge-danger">Vergebener Bewerber</span>-->
      </small>
    </h4>

    <table class="table" id="availableApplicantsTable">
        <thead>
            <tr>
              <th>ID</th>
              <th>Vornamen</th>
              <th>Nachnamen</th>
              <th>Geburtsdatum</th>
              @if (config('kitamatch_config.show_gender'))
              <th>Geschlecht</th>
              @endif
              <th>Geschwister betreut</th>
              <th>Geschwister angemeldet</th>
              @if (config('kitamatch_config.manual_points'))
              <th>Punktzahl</th>
              <th>Betreuungsbeginn</th>
              @endif
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody id="sortable">
          <!-- available applicants: automatic ranking -->
            @foreach($availableApplicants as $applicant)

            @if( $applicant->offerStatus == 1 && 
                  $applicant->status != 26 && !(count($preferences->where('id_to', '=', $applicant->aid)->whereIn('status', 1)) >= 1) &&
                    $applicant->rejectedBestOffer == 0 )

            <!-- START <tr> for manual ranking -->
              @if(count($manualRanking) == 0)
                <tr>
              @elseif(count($manualRanking) > 0)
                <tr id="item-<?php echo $manualRanking->where('id_to', '=', $applicant->aid)->first()->prid; ?>">
              @endif
            <!-- END -->

                <th scope="row">{{$applicant->aid}}</th>
                <td>{{$applicant->first_name}}</td>
                <td>{{$applicant->last_name}}</td>
                <td>{{(new Carbon\Carbon($applicant->birthday))->format('d.m.Y')}}</td>
                @if (config('kitamatch_config.show_gender'))
                <td>{{$applicant->gender}}</td>
                @endif
                <td>{{$applicant->siblingsIsPresent}}</td>
                <td>{{$applicant->sibling_applicant_id_1}}-{{$applicant->sibling_applicant_id_2}}-{{$applicant->sibling_applicant_id_3}}</td>
                <td>{{$applicant->points}}</td>
                <td>{{config('kitamatch_config.care_starts')[$applicant->care_start]}} - {{config('kitamatch_config.care_scopes')[$applicant->care_scope]}}</td>
                <td>
                    <!-- show button, if no -1 or 1 set && capacity is not fullfilled-->
                    @if ($applicant->offerStatus == 1)
                    <button type="button btn-sm" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#{{$applicant->aid}}_modal">
                                                        Angebot
                                                      </button>

                                                      <!-- Modal -->
                    <div class="modal fade" id="{{$applicant->aid}}_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Angebot an {{$applicant->first_name}} {{$applicant->last_name}}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body">
                            <div class="container-fluid">
                              <div class="row pt-1">
                                <div class="col-md-8">Präferierter Betreuungsbeginn:</div>
                                <div class="col-md-4">{{config('kitamatch_config.care_starts')[$applicant->care_start]}}</div>
                              </div>
                              <div class="row pt-1">
                                <div class="col-md-8">Präferierter Betreuungsumfang:</div>
                                <div class="col-md-4">{{config('kitamatch_config.care_scopes')[$applicant->care_scope]}}</div>
                              </div>

                              <hr>
                              <div class="row pl-2 pb-2">
                                <h5>Beginn & Umfang:</h5>
                              </div>

                              @foreach (config('kitamatch_config.care_starts') as $key_start => $start)
                                @if ($key_start != -1)
                                  <div class="row p-3">
                                  @foreach (config('kitamatch_config.care_scopes') as $key_scope => $scope)
                                    @if ($key_scope != -1)
                                      <?php $preference = $preferences->where('id_from', $program->pid . '_' . $key_start . '_' . $key_scope)->where('id_to', $applicant->aid)->first();?>
                                      @if (count($preference) == 1 && $offers[$applicant->aid]['final'] != 1)
                                        @if ($preference->status == 1)
                                          <div class="col-md-6">
                                            <button class="btn btn-info" disabled>Abgegeben</button>
                                          </div>
                                        @elseif ($preference->status == -1)
                                          <div class="col-md-6">
                                            <button class="btn btn-danger" disabled>Absage</button>
                                          </div>
                                        @endif
                                      @else  <!-- offers key does not exists -->
                                        @if ($program->openOffers[$key_start][$key_scope] < $capacities->where('care_start', '=', $key_start)->where('care_scope', '=', $key_scope)->first()->capacity && isset($servicesApplicants[$applicant->aid][$key_start][$key_scope]) && !(count($preferences->where('id_to', '=', $applicant->aid)->where('status', 1)) >= 1)) <!-- there is capacity & there is no open offer -->
                                          <div class="col-md-6">
                                            <form id="form_offer" action="{{url('/preference/program/uncoordinated/offer/' . $program->pid)}}" method="POST">
                                                {{ csrf_field() }}
                                                <input name="aid" type="hidden" value="{{$applicant->aid}}">
                                                <input name="sid" type="hidden" value="{{$program->pid}}_{{$key_start}}_{{$key_scope}}">
                                                <button id="btn_submit_offer" class="btn btn-primary">{{$start}}, {{$scope}}</button>
                                            </form>
                                          </div>
                                        @else
                                          <div class="col-md-6">
                                            <button class="btn btn-light" disabled>{{$start}}, {{$scope}}</button>
                                          </div>
                                        @endif
                                      @endif
                                    @endif
                                  @endforeach
                                </div>
                                @endif
                              @endforeach

                          </div>
                        </div>
                      </div>
                    </div>


                    @else
                      <button class="btn btn-danger btn-sm" disabled>kein Angebot verfügbar</button>
                    @endif
                </td>
                <!-- <td>
                  <form action="{{url('/preference/program/uncoordinated/waitlist/' . $program->pid)}}" method="POST">
                      {{ csrf_field() }}
                      <input type="hidden" name="aid" value="{{$applicant->aid}}">
                      <button class="btn btn-secondary btn-sm" disabled>Warteliste</button>
                    </form>
                </td> -->
              </tr>
          @endif
          @endforeach

        <!-- available applicants: manual ranking -->
        @if(count($manualRanking) > 0) <!-- check if manual ranking exists -->
          <script>
              $.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': $("input[name=_token]").val()
                  }
              });

              $(function() {
                $('#sortable').sortable({
                  axis: 'y',
                  update: function (event, ui) {
                    var order = $(this).sortable('serialize');
                    var _token = $("input[name=_token]").val();
                    var data = {"order": order, "_token": _token};
                    $.ajax({
                      data: data,
                      method: 'POST',
                      url: '{{url('/criteria/program/reorder/' . $program->pid)}}',
                      success: function(data) {
                        console.log(data);
                      }
                    });
                  }
                })
                $( "#sortable" ).disableSelection();
              });
          </script>
        @endif

        <!--- invalid applicants -->
        @foreach($availableApplicants as $applicant)
        <!--  @if($applicant->rejectedBestOffer == 0)      -->
            @if( 
              ($applicant->status == 26 && !(count($preferences->where('id_to', '=', $applicant->aid)) >= 1))
              ||
              ( !(count($preferences->where('id_to', '=', $applicant->aid)->whereIn('status', 1)) >= 1) && $applicant->offerStatus == 0)
              )

              <tr class="table-danger">
                <th scope="row">{{$applicant->aid}}</th>
                <td>{{$applicant->first_name}}</td>
                <td>{{$applicant->last_name}}</td>
                <td>{{(new Carbon\Carbon($applicant->birthday))->format('d.m.Y')}}</td>
                @if (config('kitamatch_config.show_gender'))
                <td>{{$applicant->gender}}</td>
                @endif
                <td>{{$applicant->siblingsIsPresent}}</td>
                <td>{{$applicant->sibling_applicant_id_1}}-{{$applicant->sibling_applicant_id_2}}-{{$applicant->sibling_applicant_id_3}}</td>
                <td>{{$applicant->points}}</td>
                <td>{{config('kitamatch_config.care_starts')[$applicant->care_start]}} - {{config('kitamatch_config.care_scopes')[$applicant->care_scope]}}</td>
                <td>
                  <button class="btn btn-danger btn-sm" disabled>Kein Angebot verfügbar</button>
                </td>
                <!-- <td>
                  <form action="{{url('/preference/program/uncoordinated/waitlist/' . $program->pid)}}" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" name="aid" value="{{$applicant->aid}}">
                    <button class="btn btn-secondary btn-sm" disabled>Warteliste</button>
                  </form>
                </td> -->
              </tr>
            @endif
      <!--    @endif    -->
        @endforeach
<!--
        @foreach($availableApplicants as $applicant)
          @if($applicant->rejectedBestOffer == 1)

            <tr class="table-danger">
              <th scope="row">{{$applicant->aid}}</th>
              <td>{{$applicant->first_name}}</td>
              <td>{{$applicant->last_name}}</td>
              <td>{{(new Carbon\Carbon($applicant->birthday))->format('d.m.Y')}}</td>
              <td>{{$applicant->gender}}</td>
              <td>{{$applicant->points}}</td>
              <td>{{config('kitamatch_config.care_starts')[$applicant->care_start]}} - {{config('kitamatch_config.care_scopes')[$applicant->care_scope]}}</td>
              <td>
                <button class="btn btn-danger btn-sm" disabled>Rejected Best Offer</button>
              </td>
              <td>
                <form action="{{url('/preference/program/uncoordinated/waitlist/' . $program->pid)}}" method="POST">
                  {{ csrf_field() }}
                  <input type="hidden" name="aid" value="{{$applicant->aid}}">
                  <button class="btn btn-secondary btn-sm" disabled>Warteliste</button>
                </form>
              </td>
            </tr>
            @endif
        @endforeach
       -->
      </tbody>
    </table>

    @if(count($manualRanking) == 0)
    <a href="{{url('/criteria/program/manual/' . $program->pid)}}" style="float:right;"><button class="btn" >Manuelle Rangliste</button></a>
    @endif
</div>

</div>

<div class="row justify-content-center">
    <div class="col-md-6">
        <hr class="mb-4">
        <a href="{{url('/program/' . $program->pid)}}"><button class="btn btn-primary btn-lg btn-block">Stammdaten</button></a>
        <hr class="mb-4">
        <a href="{{url('/criteria/program/' . $program->pid)}}"><button class="btn btn-primary btn-lg btn-block" disabled>Kriterien verändern</button></a>
    </div>
</div>

@endsection
