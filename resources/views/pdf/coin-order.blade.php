<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Bestelling #{{ $order->id }}</title>
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
        .order-info {
            margin-bottom: 15px;
        }
        .customer-info {
            margin-bottom: 15px;
        }
        .order-details {
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
        .total {
            text-align: right;
            font-weight: bold;
            font-size: 1.1em;
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
        <h1>Bestelling #{{ $order->id }}</h1>
        <p>Datum: {{ $order->created_at->format('d-m-Y H:i') }}</p>
    </div>

    <div class="customer-info">
        <h2>Uw gegevens</h2>
        <p><strong>Naam:</strong> {{ $order->name }}</p>
        <p><strong>E-mail:</strong> {{ $order->email }}</p>
        @if($user)
            @php
                $addressParts = [];
                if ($user->street && $user->number) {
                    $addressParts[] = $user->street . ' ' . $user->number;
                } elseif ($user->street) {
                    $addressParts[] = $user->street;
                }
                if ($user->zipcode && $user->city) {
                    $addressParts[] = $user->zipcode . ' ' . $user->city;
                } elseif ($user->zipcode) {
                    $addressParts[] = $user->zipcode;
                } elseif ($user->city) {
                    $addressParts[] = $user->city;
                }
            @endphp
            @if(!empty($addressParts))
                <p><strong>Adres:</strong> {{ implode(', ', $addressParts) }}</p>
            @endif
            @if($user->phonenumber)
                <p><strong>Telefoon:</strong> {{ $user->phonenumber }}</p>
            @endif
        @endif
    </div>

    <div class="order-details">
        <h2>Bestelde munten</h2>
        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Aantal</th>
                    <th>Prijs per stuk</th>
                    <th>Subtotaal</th>
                </tr>
            </thead>
            <tbody>
                @if($order->silver_coins > 0)
                <tr>
                    <td>Zilveren munten</td>
                    <td>{{ $order->silver_coins }}</td>
                    <td>€{{ number_format(config('coins.prices.silver_coin'), 2) }}</td>
                    <td>€{{ number_format($order->silver_coins * config('coins.prices.silver_coin'), 2) }}</td>
                </tr>
                @endif
                @if($order->gold_coins > 0)
                <tr>
                    <td>Gouden munten</td>
                    <td>{{ $order->gold_coins }}</td>
                    <td>€{{ number_format(config('coins.prices.gold_coin'), 2) }}</td>
                    <td>€{{ number_format($order->gold_coins * config('coins.prices.gold_coin'), 2) }}</td>
                </tr>
                @endif
                <tr>
                    <td colspan="3">Transactiekosten</td>
                    <td>€{{ number_format(config('coins.prices.payment_fee'), 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="total">
            Totaalbedrag: €{{ number_format($order->total_amount, 2) }}
        </div>
    </div>

    <div class="footer">
        <div>
            <img src="{{ public_path('images/logo.png') }}" alt="HHG Rijssen" class="footer-logo">
            <p>&copy; {{ date('Y') }} HHG Rijssen</p>
        </div>
        <div class="footer-info">
            <p>Bestelling #{{ $order->id }}</p>
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
