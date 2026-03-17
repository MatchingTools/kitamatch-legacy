@extends('layouts.app')

@section('content')

<div class="row justify-content-center">
    <div class="col-md-6">
        <h2>Add Applicant</h2>

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
</div>
<div class="row justify-content-center">
    <div class="col-md-6  my-3 p-3 bg-white rounded box-shadow">
        <form action="{{url('/applicant/add/'}}" method="POST" class="">
            {{ csrf_field() }}

            <div class="form-group row">
                <label for="firstName" class="col-sm-3 col-form-label">First name</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" name="firstName" id="firstName" placeholder="" value="{{old('firstName')}}" required="">
                </div>
            </div>
            <div class="form-group row">
                <label for="lastName"  class="col-sm-3 col-form-label">Last name</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" name="lastName" id="lastName" placeholder="" value="{{old('lastName')}}" required="">
                </div>
            </div>
            <div class="form-group row">
                <label for="birthday"  class="col-sm-3 col-form-label">Birthday</label>
                <div class="col-sm-8">
                    <input type="date" class="form-control" name="birthday" id="birthday" placeholder="" value="{{old('birthday')}}" required="">
                </div>
            </div>
            <div class="form-group row">
                <label for="gender"  class="col-sm-3 col-form-label">Gender</label>
                <div class="col-sm-8">
                     {!! Form::select('gender', array('M' => 'M',
                                                      'W' => 'W',
                                                      'Divers' => 'Divers'),
                                              null,
                        array('id' => 'gender', 'class' => 'form-control') )  !!}
                </div>
            </div>

            <hr class="mb-4">
            <button class="btn btn-primary btn-lg btn-block" type="submit">Add applicant</button>
        </form>
    </div>
</div>

@endsection
