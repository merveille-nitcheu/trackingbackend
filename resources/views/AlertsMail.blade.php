<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Alert Sensor</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; margin: 0;">
    <h1 style="padding: 12px;">Notifications traqueurs</h1>

    @if(!empty($notifications))
    <table style="border-collapse: collapse; width: 100%;">
        <tr style="background-color: #7e82f4; color: white;">
            <th style="padding: 12px; text-align: left;">Sensor_reference</th>
            <th style="padding: 12px; text-align: left;">Battery</th>
            <th style="padding: 12px; text-align: left;">Probleme</th>
            <th style="padding: 12px; text-align: left;">Derniere émission</th>
        </tr>
@foreach ($notifications as $notification)

<tr style="border: 1px solid #ddd; background-color: #f2f2f2;">
    <td style="padding: 8px;">{{ $notification->sensor_reference }}</td>
    <td style="padding: 8px;">{{ round($notification->batteryPercent,1) }} %</td>
    <td style="padding: 8px;">{{ $notification->description }}</td>
    <td style="padding: 8px;">{{ $notification->created_at }}</td>

</tr>

@endforeach


    </table>
    @else
        <p style="padding: 8px;">Pas de notification.</p>
    @endif
</body>
</html>
