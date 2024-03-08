@component('mail::message')
# Congrats!

<div>You have successfully registered to our <b>{{ config('app.name') }}</b>.</div>
<div>
    To start, kindly check the attached API manual for integrating the connection between <b>{{ config('app.name') }}</b> and <b style="text-transform:Capitalize">{{ ucfirst($data["company_name"]) }}</b>.<br>
</div>

<div style="margin:15px 0;">Below are the details for the API parameters.</div>

<div><b>API Endpoint:</b> {{ env('API_ENDPOINT') }}</div>
<div><b>API Code:</b>{{$data["code"]}}</div>
<div><b>API Token:</b> {{$data["token"]}}</div>
<div><b>Secret Key:</b> {{$data["secret"]}}</div>


<div style="margin-top:20px;">Thanks,</div>
{{ config('app.name') }}
@endcomponent


