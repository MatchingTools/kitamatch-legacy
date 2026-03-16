@extends('layouts.app')

@section('content')

<div class="row justify-content-center">
  <div class="col-md-8">
    <h4>Kitagruppe hinzufügen</h4>
  </div>
</div>
<div class="row justify-content-center">
<div class="col-md-8  my-3 p-3 bg-white rounded box-shadow">
        <form action="{{url('/program/add/' . $provider->proid)}}" method="POST" class="">
            {{ csrf_field() }}

            <div class="form-group row">
                <label for="name" class="col-sm-3 col-form-label">Gruppenname</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control" name="name" id="name" placeholder="" value="" required>
                    @if ($errors->has('name'))
                      <div class="invalid-feedback">
                          Bitte geben Sie einen Namen ein.
                      </div>
                    @endif
                </div>
                <label for="type" class="col-sm-4 col-form-label">Automatisierte Koordinierung?</label>
                <div class="col-sm-2">
                    <!--<input type="checkbox" class="form-control" name="type" id="type">-->
                    <select class="form-control" name="type">
                      <option value="0">Ja</option>
                      <option value="1" selected>Nein</option>
                    </select>
                </div>
            </div>

            <hr class="mb-4">
            <button class="btn btn-primary btn-lg btn-block" type="submit">Gruppe hinzufügen</button>
        </form>
    </div>
</div>

@endsection
