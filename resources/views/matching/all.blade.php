
@extends('layouts.app')

@section('content')
<h4>Current Matches</h4>

<table class="table">
    <thead>
        <tr>
            <th>Program</th>
            <th>Applicant</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($matches as $match)
            <tr>
              <td><a target="_blank" href="/preference/program/{{$match->pid}}">{{$match->program_name}}</a></td>
              <td><a target="_blank" href="/preference/applicant/{{$match->aid}}">{{$match->applicant_name}}</a></td>
              <td>{{$match->status}}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
