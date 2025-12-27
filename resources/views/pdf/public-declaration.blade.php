<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Declaratie #{{ $declaration->id }}</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 10px;
        }
        .declaration-info {
            margin-bottom: 15px;
        }
        .personal-info {
            margin-bottom: 15px;
        }
        .service-info {
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 0.9em;
        }
        th, td {
            padding: 6px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f9fa;
        }
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #eee;
            font-size: 0.8em;
            color: #666;
            display: flex;
            justify-content: space-between;
        }
        .footer-logo {
            max-width: 100px;
        }
        .footer-info {
            font-size: 0.8em;
        }
        .footer-address {
            font-size: 0.8em;
        }
        h1 {
            font-size: 1.5em;
            margin: 5px 0;
        }
        h2 {
            font-size: 1.2em;
            margin: 5px 0;
        }
        p {
            margin: 3px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/logo.png') }}" alt="HHG Rijssen" class="logo">
        <h1>Declaratie #{{ $declaration->id }}</h1>
        <p>Datum: {{ $declaration->created_at->format('d-m-Y H:i') }}</p>
    </div>

    <div class="personal-info">
        <h2>Persoonlijke gegevens</h2>
        <p><strong>Naam:</strong> {{ $declaration->name }}</p>
        <p><strong>E-mail:</strong> {{ $declaration->email }}</p>
        <p><strong>Adres:</strong> {{ $declaration->street }} {{ $declaration->number }}</p>
        <p><strong>Postcode:</strong> {{ $declaration->zipcode }} {{ $declaration->city }}</p>
    </div>

    <div class="service-info">
        <h2>Dienst informatie</h2>
        <table>
            <thead>
                <tr>
                    <th>Onderdeel</th>
                    <th>Waarde</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Datum van dienst</td>
                    <td>{{ $declaration->date_of_service->format('d-m-Y') }}</td>
                </tr>
                <tr>
                    <td>Tijd van dienst 1</td>
                    <td>{{ $declaration->time_of_service_1 }}</td>
                </tr>
                @if($declaration->time_of_service_2)
                <tr>
                    <td>Tijd van dienst 2</td>
                    <td>{{ $declaration->time_of_service_2 }}</td>
                </tr>
                @endif
                <tr>
                    <td>Kilometers</td>
                    <td>{{ $declaration->kilometers }} km</td>
                </tr>
                <tr>
                    <td>Status</td>
                    <td>{{ ucfirst($declaration->status) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="calculation-info">
        <h2>Declaratie berekening</h2>
        <table>
            <thead>
                <tr>
                    <th>Omschrijving</th>
                    <th>Aantal</th>
                    <th>Prijs per stuk</th>
                    <th>Subtotaal</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $timeslotPrice = 130.00;
                    $kilometerPrice = 0.35;
                    $numberOfTimeslots = (!empty($declaration->time_of_service_1) && !empty($declaration->time_of_service_2)) ? 2 : 1;
                    $timeslotTotal = $numberOfTimeslots * $timeslotPrice;
                    $kilometerTotal = $declaration->kilometers * $kilometerPrice;
                @endphp
                <tr>
                    <td>Diensten</td>
                    <td>{{ $numberOfTimeslots }}</td>
                    <td>€{{ number_format($timeslotPrice, 2) }}</td>
                    <td>€{{ number_format($timeslotTotal, 2) }}</td>
                </tr>
                <tr>
                    <td>Kilometers</td>
                    <td>{{ $declaration->kilometers }} km</td>
                    <td>€{{ number_format($kilometerPrice, 2) }}</td>
                    <td>€{{ number_format($kilometerTotal, 2) }}</td>
                </tr>
                <tr style="font-weight: bold; border-top: 2px solid #333;">
                    <td colspan="3">Totaal</td>
                    <td>€{{ number_format($declaration->amount, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="declaration-info">
        <h2>Toelichting</h2>
        <p>{{ $declaration->explanation }}</p>
    </div>

    <div class="footer">
        <div>
            <img src="{{ public_path('images/logo.png') }}" alt="HHG Rijssen" class="footer-logo">
            <p>&copy; {{ date('Y') }} HHG Rijssen</p>
        </div>
        <div class="footer-info">
            <p>Declaratie #{{ $declaration->id }}</p>
        </div>
        <div class="footer-address">
            <p><strong>HHG Rijssen</strong></p>
            <p>Haarstraat 95, 7462 AL Rijssen</p>
            <p>Tel: +31 6 43 11 67 76</p>
            <p>kerkvoogdij@hhgrijssen.nl</p>
        </div>
    </div>
</body>
</html>
