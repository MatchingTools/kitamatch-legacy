@extends('layouts.app')

@section('content')

<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
 <!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- jQuery UI -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

<div class="row justify-content-center">
    <div class="col-md-8" id="list">
        <h4>Kriterien</h4>

      </div>
  </div>

        <script>
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });

        $(function() {
            // sort
            $('#sortable').sortable({
                axis: 'y',
                update: function (event, ui) {
                    var order = $(this).sortable('serialize');
                    var _token = $("input[name=_token]").val();
                    var data = {"order": order, "_token": _token};
                    $.ajax({
                        data: data,
                        type: 'POST',
                        url: '{{url('/criteria')}}',
                        success: function(data) {
                            console.log(data);
                        }
                    });
                }
            });
            // deactivate
            $('#sortable').on('click', '.deactivate', function() {
                $(this).closest('li').remove(); //decativate?!
                var criteriaId = {'itemId': $(this).closest('li').attr('id')};
                $.ajax({
                        data: criteriaId,
                        type: 'POST',
                        url: '{{url('/criteria/delete/')}}' . criteriaId, //concatinate
                        success: function(criteriaId) {
                            console.log(criteriaId);
                        }
                    });
            });

            $('#sortable').disableSelection();
        });
        </script>

        <div class="row justify-content-center">

            <div class="col-md-6">

        <ul id="sortable" class="list-group">
            {{ csrf_field() }}
            @foreach ($criteria as $criterium)
                <li id="item-{{$criterium->cid}}" class="ui-state-default list-group-item d-flex justify-content-between align-items-center">
                  <span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
                  <span class="col-8">{{$criterium->criterium_value_description}}</span>
               <!--   <a class="deactivate" href="#list"><span class="badge badge-secondary badge-pill">x</span></a> -->
                </li>
             @endforeach
        </ul>
</div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <hr class="mb-4">
        @if ($criterium->program == 1)
        <a href="{{url('/preference/program/' . $criterium->p_id)}}"><button class="btn btn-primary btn-lg btn-block">Zurück zu Angeboten</button></a>
        @else
        <a href="{{url('/provider/program/' . $criterium->p_id)}}"><button class="btn btn-primary btn-lg btn-block">Zurück zum Träger</button></a>
        @endif
    </div>
</div>


@endsection
