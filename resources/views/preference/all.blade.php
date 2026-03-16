<!DOCTYPE html>
<html>
  <head>
	<title>Preferences</title>
  </head>
<body>
    <table>
        <tr>
            <th>Id from</th>
            <th>Id to</th>
            <th>Preference kind</th>
            <th>Rank</th>
            <th>Status</th>
        </tr>
        @foreach($preferences as $preference)
            <tr>
                <td>{{$preference->id_from}}</td>
                <td>{{$preference->id_to}}</td>
                <td>{{$preference->pr_kind}}</td>
                <td>{{$preference->rank}}</td>
                <td>{{$preference->status}}</td>
            </tr>
        @endforeach
    </table>
</body>
</html>
