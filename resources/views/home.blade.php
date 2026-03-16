@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    You are logged in!
                    
                    Applicant: Check <a href="/applicant">applicant</a> or <a href="/preference/applicant">preferences</a>.
                    Program: Check <a href="/program">program</a> or <a href="/preference/program/">preferences</a>.
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
