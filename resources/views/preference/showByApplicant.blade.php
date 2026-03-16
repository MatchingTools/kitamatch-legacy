@extends('layouts.app')

@section('content')

<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

<div class="row justify-content-center">
    <div class="col-md-8">
        @if (count($preferences) < 3)
        <div class="alert alert-warning" role="alert">
          Es wird empfohlen mindestens 3 Wünsche anzugeben.
        </div>
        @endif

        <!--<h2>{{$applicant->first_name}} {{$applicant->last_name}}: </h2>-->
        <h2>Rangliste der Wunscheinrichtungen <small class="text-muted">{{$applicant->first_name}} {{$applicant->last_name}} </small></h2>
      </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8  my-3 p-3 bg-white rounded box-shadow">
        @if (count($programs)>0)
        <form action="{{url('/preference/applicant/' . $applicant->aid)}}" method="POST" class="form-horizontal">
            {{ csrf_field() }}

            <div class="form-group row">
                <label for="to" class="col-sm-2 col-form-label">Kitagruppe</label>
                <div class="col-sm-6">
                    {!! Form::select('to', $programs,false,
                        array('id' => 'preference-id-to',
                              'class' => 'form-control')
                    )  !!}
                </div>
                <div class="col-sm-2">
                  <button type="submit" class="btn btn-primary">Hinzufügen</button>
                </div>
            </div>
        </form>
        @else
        <button type="submit" class="btn btn-secondary" disabled>Alle Kitas  wurden ausgewählt.</button>
        @endif
    </div>
</div>

<!-- List of programs -->
<div class="row justify-content-center">
      @if (count($preferences) > 0)
    <div class="col-md-8">

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
                $( "#sortable" ).disableSelection();
            });
        </script>

        <ul id="sortable" class="list-group">
            {{ csrf_field() }}
            <?php $i = 1; ?>
            @foreach ($preferences as $preference)
                <li id="item-{{$preference->prid}}" class="ui-state-default list-group-item d-flex justify-content-between align-items-center" style="margin-bottom: 10px;"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
                    <span class="rank badge badge-dark">{{$i}}</span>
                    <span class="col-8">{{$preference->programName}}</span>
                    <a class="delete" href="#"><span class="badge badge-secondary badge-pill">x</span></a>
                </li>
                <?php $i++; ?>
             @endforeach
        </ul>
        </div>
        @else
          <div class="col-md-8">
            Please add preferences.
          </div>
        @endif
</div>

<!-- Go Back -->
<div class="row justify-content-center">
    <div class="col-md-8">
        <hr class="mb-4">
        <a href="{{url('/applicant/' . $applicant->aid)}}"><button class="btn btn-primary btn-lg btn-block">Bewerberinformationen</button></a>
    </div>
</div>

@endsection
