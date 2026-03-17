@extends('layouts.app')

@section('content')

<script src='https://www.google.com/recaptcha/api.js'></script>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card box-shadow">
                <div class="card-header">Registrierung</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">E-Mail-Adresse</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">Passwort</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">Wiederhole Passwort</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="account-type" class="col-md-4 col-form-label text-md-right">Konto</label>

                            <div class="col-md-6">
                                <select id="account-type" class="form-control" name="accountType" required>
                                    <option value="1">Bewerber</option>
                                    <!--<option value="2">Öffentliche Kita</option>-->
                                    <!--<option value="3">Private Kita</option>-->
                                    <option value="4">Kindertagesstätte</option>
                                </select>
                            </div>

                        </div>
                        <!-- <div class="form-group row mb-0"> -->
                            <!-- <div class="col-md-6 offset-md-4">
                              <div class="g-recaptcha" data-sitekey="{{env('GOOGLE_RECAPTCHA_KEY')}}"></div>
                              @if ($errors->has('g-recaptcha-response'))
                                  <span class="invalid-feedback" style="display: contents;">
                                      <strong>{{ $errors->first('g-recaptcha-response') }}</strong>
                                  </span>
                              @endif
                              <br>
                            </div>
                          </div> -->

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Registrieren
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
