@extends('layouts.app')

@section('content')

<div class="row justify-content-center">
    <div class="col-md-8">
      @if ($errors->any())
      <div class="alert alert-danger">
          <ul>
              @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
              @endforeach
          </ul>
      </div>
      @endif

      @if ($applicant->status == 26)
      <div class="alert alert-success" role="alert">
        Sie wurden erfolgreich zugeteilt und haben einen Platz erhalten.
      </div>
      @endif

        <h2>Bewerberinformationen <small class="text-muted">({{$applicant->status}})</small></h2>
      </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8 my-3 p-3 bg-white rounded box-shadow">

        <form action="{{url('/applicant/' . $applicant->aid)}}" method="POST" class="">
            {{ csrf_field() }}

            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="firstName">Vorname*</label>
                    <input type="text" class="form-control" name="firstName" id="firstName" placeholder="" value="{{$applicant->first_name}}" required>
              </div>
              <div class="form-group col-md-6">
                <label for="lastName">Nachname*</label>
                    <input type="text" class="form-control" name="lastName" id="lastName" placeholder="" value="{{$applicant->last_name}}" required>
            </div>
          </div>
            <div class="form-row" style="pointer-events: none; opacity: 0.7;">
              <div class="form-group col-md-4">
                <label for="birthday">Geburtstag*</label>
                    <input type="date" class="form-control" name="birthday" id="birthday" placeholder="" value="<?php if ($applicant->birthday) { echo $applicant->birthday->format('Y-m-d'); } ?>" required>
              </div>
              <div class="form-group col-md-2">
                <label for="age_cohort">Altersgruppe*</label>
                  {!! Form::select('age_cohort', $config['age_cohorts'],
                                           $applicant->age_cohort,
                   array('id' => 'age_cohort', 'class' => 'form-control', 'required') )  !!}
              </div>
              <div class="form-group col-md-6">
                <label for="gender">Geschlecht*</label>
                     {!! Form::select('gender', array('M' => 'M',
                                                      'W' => 'W',
                                                      'Divers' => 'Divers'),
                                                $applicant->gender,
                        array('id' => 'gender', 'class' => 'form-control', 'required') )  !!}
            </div>
          </div>

            <hr class="mb-4">

@foreach ($criteria_names as $criterium_name)
<?php
  $criterium_values = $criteria_values->where('criterium_name', '=', $criterium_name->criterium_name);
?>

<div class="form-group row">
  <label for="{{$criterium_name->criterium_name}}" class="col-sm-6 col-form-label">{{$criterium_name->criterium_question}}</label>
  <div class="col-sm-6">
    <select name="{{$criterium_name->criterium_name}}" id="{{$criterium_name->criterium_name}}" class="form-control">
      <option value="0">Bitte auswählen...</option>
    @foreach ($criterium_values as $value)
      @if (in_array($value->criterium_value,$applicant->toArray()))
        <option value="{{$value->criterium_value}}" selected>{{$value->criterium_value_description}}</option>
      @else
        <option value="{{$value->criterium_value}}">{{$value->criterium_value_description}}</option>
      @endif
    @endforeach
    </select>
  </div>
</div>

@endforeach

<hr class="mb-4">

        @if (config('kitamatch_config.has_diff_starts'))
            <div class="form-group row">
                <label for="care_start" class="col-sm-6 col-form-label">{{config('kitamatch_config.care_starts_text')['question_select']}}</label>
                <div class="col-sm-6">
                     {!! Form::select('care_start', $config['care_starts'],
                      $applicant->care_start,
                      array('id' => 'care_start', 'class' => 'form-control') )  !!}
                </div>
            </div>
        @endif

        @if (config('kitamatch_config.has_diff_scopes'))
            <div class="form-group row">
              <label for="care_scope" class="col-sm-6 col-form-label">{{config('kitamatch_config.care_scopes_text')['question_select']}}</label>
              <div class="col-sm-6">
                   {!! Form::select('care_scope', $config['care_scopes'],
                    $applicant->care_scope,
                    array('id' => 'care_scope', 'class' => 'form-control') )  !!}
              </div>
            </div>
        @endif

            <hr class="mb-4">

        @if (config('kitamatch_config.has_diff_starts'))
            <div class="form-group row">
              <label for="alternative_start" class="col-sm-6 col-form-label">
                {{config('kitamatch_config.care_starts_text')['question_bool']}}</label>
              <div class="col-sm-6">
                   {!! Form::select('alternative_start', array(
                    '-1' => 'Bitte auswählen...',
                    '1' => 'Ja',
                    '0' => 'Nein'
                    ),
                    $applicant->alternative_start,
                    array('id' => 'alternative_start', 'class' => 'form-control') )  !!}
              </div>
            </div>
        @endif

        @if (config('kitamatch_config.has_diff_scopes'))
            <div class="form-group row">
                <label for="alternative_scope" class="col-sm-6 col-form-label">{{config('kitamatch_config.care_scopes_text')['question_bool']}}</label>
                <div class="col-sm-6">
                     {!! Form::select('alternative_scope', array(
                      '-1' => 'Bitte auswählen...',
                      '1' => 'Ja',
                      '0' => 'Nein'
                      ),
                      $applicant->alternative_scope,
                      array('id' => 'alternative_scope', 'class' => 'form-control') )  !!}
                </div>
            </div>
            @endif

            <hr class="mb-4">
            @if (!$data['isSet'])           
            <button class="btn btn-primary btn-lg btn-block" type="submit">Aktualisieren</button>
            @else
            <button class="btn btn-primary btn-lg btn-block" type="submit" disabled>Aktualisieren</button>
            @endif
        </form>
    </div>
</div>

<div class="row justify-content-center pt-5 ">
  <div class="col-md-8" id="list">
    <h2>Rangliste der Wunscheinrichtungen</h2>
  </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

<div class="row justify-content-center">
    <div class="col-md-8  my-3 p-3 bg-white rounded box-shadow">
    @if (count($programs)>0)
    <form action="{{url('/preference/applicant/' . $applicant->aid)}}" method="POST" class="form-horizontal">
        {{ csrf_field() }}

        <div class="form-group row">
            <label for="to" class="col-sm-2 col-form-label">Kitagruppe:</label>
            <div class="col-sm-6">
                {!! Form::select('to', $programs,false,
                    array('id' => 'preference-id-to',
                          'class' => 'form-control')
                )  !!}
            </div>
            <div class="col-sm-2">
              @if (!$data['isSet'])
                <button type="submit" class="btn btn-primary">Hinzufügen</button>
              @else    
                <button type="submit" class="btn btn-primary" disabled>Hinzufügen</button>
              @endif
            </div>
        </div>
    </form>
    @else
    <button type="submit" class="btn btn-secondary" disabled>Alle Kitas  wurden ausgewählt.</button>
    @endif


<!-- List of programs -->
  @if (count($preferences) > 0)

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
                return $(this).parent().index("li")+1;
              });
              var order = $(this).sortable('serialize');
              var _token = $("input[name=_token]").val();
              var data = {"order": order, "_token": _token};
              $.ajax({
                data: data,
                type: 'POST',
                url: '{{url('/preference/applicant/reorder/' . $preferences->first()->id_from)}}',
                success: function(data) {
                  console.log(data);
                }
              });
            }
          })
            .on('click', '.delete', function() {
                $(this).closest('li').remove();
                var data = {'itemId': $(this).closest('li').attr('id')};
                $.ajax({
                        data: data,
                        type: 'POST',
                        url: '{{url('/preference/applicant/delete/' . $preferences->first()->id_from)}}',
                        success: function(data) {
                            console.log(data);
                        }
                    });
            });
            $("#sortable" ).disableSelection();
        });
    </script>
      @if (!$data['isSet'])
        <ul id="sortable" class="list-group">
            {{ csrf_field() }}
            <?php $i = 1; ?>
            @foreach ($preferences as $preference)
                <li id="item-{{$preference->prid}}" class="ui-state-default list-group-item d-flex justify-content-between align-items-center" style="margin-bottom: 10px;"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
                    <span class="rank badge badge-dark">{{$i}}</span>
                    <span class="col-8">{{$preference->programName}}</span>
                    <a class="delete" href="#list"><span class="badge badge-secondary badge-pill">x</span></a>
                </li>
                <?php $i++; ?>
            @endforeach
        </ul>
      @else
        <ul  class="list-group">
            {{ csrf_field() }}
            <?php $i = 1; ?>
            @foreach ($preferences as $preference)
                <li id="item-{{$preference->prid}}" class="ui-state-default list-group-item d-flex justify-content-between align-items-center" style="margin-bottom: 10px;"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
                    <span class="rank badge badge-dark">{{$i}}</span>
                    <span class="col-8">{{$preference->programName}}</span>
                    <a class="delete" href="#list"><span class="badge badge-secondary badge-pill"></span></a>
                </li>
                <?php $i++; ?>
            @endforeach
        </ul>
      @endif
    </div>
  @else
    <div class="col-md-8">
      Bitte fügen Sie ihre Wunscheinrichtungen hinzu. Hierfür muss zunächst das Formular ausgefüllt werden.
    </div>
  @endif
</div>


<!-- Go to program list -->
<!--<div class="row justify-content-center">
    <div class="col-md-8">
        <hr class="mb-4">
        <a href="{{url('/preference/applicant/' . $applicant->aid)}}"><button class="btn btn-primary btn-lg btn-block">Rangliste der Wunscheinrichtungen</button></a>
    </div>
</div>-->

<!--<div class="row justify-content-center">
    <div class="col-md-8">
      @if ($applicant->status == 25)
      <button class="btn btn-lg btn-block">Priorität ist gesetzt</button>
      @endif
      @if ($applicant->status != 25)
      <a href="{{url('/applicant/setPriority/' . $applicant->aid)}}"><button class="btn btn-primary btn-lg btn-block">Priorität setzen</button></a>
      @endif
</div>-->

@endsection
