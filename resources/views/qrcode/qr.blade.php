<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="container">
        <img src="{{url('/images/logo_akb.png')}}" alt="Image" width="300" height="300">        
        <div>            
            <img src="data:image/svg;base64, {{ $qrcode }} ">
        </div>
        <h3>Printed {{ $date }}, {{ $thn }} {{ $jam }}</h3>
        <h3 class="print">Printed by {{ $orang }}</h3>
        <br>
       
        <span>....................................</span>
        <h2>FUN PLACE TO GRILL</h2>
        <span>....................................</span>
    </div>
</body>

<style>
    .container {
        text-align: center;
    }
    .print {
        font-weight: normal;
    }
</style>
</html>