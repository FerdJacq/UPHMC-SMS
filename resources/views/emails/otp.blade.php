@component('mail::message')
# Two-Factor Authentication

<div>Your verification code:</div>
<div><b style="font-size:20px;color:#4776e6;">{{$data["code"]}}</b></div>
<div style="margin-top:5px;">
The verification code will be valid for 30 minutes. <br>
Please do not share this code with anyone.

<div style="margin:10px 5px;">
    <table style="font-color:white;font-size:14px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;color:#3d4452;padding-top:6px;padding-bottom:6px">
        <tr>
            <td style="color:#808080">Operating system: </td>
            <td>{{$data["platform"]}}</td>
        </tr>
        <tr>
            <td style="color:#808080">Browser: </td>
            <td>{{$data["browser"]}}</td>
        </tr>
        <tr>
            <td style="color:#808080">IP Address: </td>
            <td>{{$data["ip"]}}</td>
        </tr>
    </table>
</div>


Don’t recognize this activity?<br>
Please reset your <a href="#">password</a> and contact <a href="#">customer support</a> immediately. 
</div>

<div style="font-style:italic;margin-top:10px;">This is an automated message, please do not reply. </div>


<div style="margin-top:20px;">Thanks,</div>
{{ config('app.name') }}
@endcomponent


