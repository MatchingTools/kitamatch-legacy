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

      @if ($program->status == 10)
      <div class="alert alert-danger" role="alert">
        Required information missing. Please fullfill your profil.
      </div>
      @endif

      @if ($program->status == 13)
      <div class="alert alert-danger" role="alert">
        You are inactive for at least 7 days.
      </div>
      @endif

        <h2>Stammdaten der Gruppe verwalten</h2>
</div>
</div>
<div class="row justify-content-center">
    <div class="col-md-8 my-3 p-3 bg-white rounded box-shadow">
        <form action="{{url('/program/' . $program->pid)}}" method="POST">
            {{ csrf_field() }}

            <div class="form-group row">
                <label for="name" class="col-sm-2 col-form-label">Gruppenname</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" id="name" name="name" value="{{$program->name}}" >
                </div>
            </div>
            <div class="form-group row" style="pointer-events: none; opacity: 0.7;">
                <label for="age_cohort" class="col-sm-2 col-form-label">Altersgruppe</label>
                <div class="col-sm-10">
                  {!! Form::select('age_cohort', array('0' => '---',
                  '1' => 'U2',
                  '2' => '2',
                  '3' => 'Ü3'),
                                           $program->age_cohort,
                   array('id' => 'age_cohort', 'class' => 'form-control') )  !!}
                </div>
            </div>

            <hr class="mb-4">

            <!-- Email but with user-table! -->

            Freie Plätze
            @foreach (config('kitamatch_config.care_starts') as $care_start_key => $care_start)
              @foreach (config('kitamatch_config.care_scopes') as $care_scope_key => $care_scope)
                @if ($care_start_key != -1 and $care_scope_key != -1)
                <?php $capacity = $capacities->where('care_start', '=', $care_start_key)->where('care_scope', '=', $care_scope_key)->first(); ?>
                <div class="form-group row">
                  <label for="capacity" class="col-sm-2 col-form-label">{{$care_start}}, {{$care_scope}}</label>
                  <div class="col-sm-10">
                    <input type="number" min="0" class="form-control" id="{{'capacity_' . $capacity->id}}" name="{{'capacity_' . $capacity->id}}" placeholder="" value="{{$capacity->capacity}}">
                  </div>
                </div>
                @endif
              @endforeach
            @endforeach


            <hr class="mb-4">
            <button class="btn btn-light btn-lg btn-block" type="submit">Änderungen speichern</button>
        </form>
    </div>
</div>

<br>

<div class="row justify-content-center">
    <div class="col-md-8">
        <hr class="mb-4">

        @if ($program->coordination == 1)
            <a href="{{url('/preference/program/' . $program->pid)}}"><button class="btn btn-primary btn-lg btn-block">Rangliste einsehen</button></a>
        @else
            <a href="{{url('/preference/program/' . $program->pid)}}"><button class="btn btn-primary btn-lg btn-block">Zum Koordinierungsverfahren</button></a>
        @endif

        <!-- to do: add provider button if it has a provider-->
        @if ($program->proid)

        <hr class="mb-4">

        <a href="{{url('/provider/' . $program->proid)}}"><button class="btn btn-light btn-lg btn-block">Zurück zur Kita</button></a>
        @endif
    </div>
</div>

@endsection
