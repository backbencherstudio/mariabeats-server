<?php
$franchaisee = $franchaisee;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <!-- make it more beautiful template -->
    <div style="background-color: #f5f5f5; padding: 20px; border-radius: 10px;">
        <h1 style="font-size: 24px; font-weight: bold;">Hello Gulf Franchisee Hub,</h1>
        <p>New Franchaisee Request from {{ $franchaisee->name }}.</p>
        <p>Here are the details of the new franchaisee request:</p>
        <ul style="display: flex-col; flex-direction: column; gap: 10px;">
            <li style="font-size: 16px; font-weight: bold;">Brand Name: <span style="font-size: 14px; font-weight: normal;">{{ $franchaisor->name }}</span></li>
            <li style="font-size: 16px; font-weight: bold;">Name: <span style="font-size: 14px; font-weight: normal;">{{ $franchaisee->name }}</span></li>
            <li style="font-size: 16px; font-weight: bold;">Email: <span style="font-size: 14px; font-weight: normal;">{{ $franchaisee->email }}</span></li>
            <li style="font-size: 16px; font-weight: bold;">Phone: <span style="font-size: 14px; font-weight: normal;">{{ $franchaisee->phone_number }}</span></li>
            <li style="font-size: 16px; font-weight: bold;">Country: <span style="font-size: 14px; font-weight: normal;">{{ $franchaisee->country }}</span></li>
            <li style="font-size: 16px; font-weight: bold;">Preferred Location: <span style="font-size: 14px; font-weight: normal;">{{ $franchaisee->preferred_location }}</span></li>
            <li style="font-size: 16px; font-weight: bold;">Investment Amount: <span style="font-size: 14px; font-weight: normal;">${{ $franchaisee->investment_amount }}</span></li>
            <li style="font-size: 16px; font-weight: bold;">Timeframe: <span style="font-size: 14px; font-weight: normal;">{{ $franchaisee->timeframe }}</span></li>
            <li style="font-size: 16px; font-weight: bold;">Message: <span style="font-size: 14px; font-weight: normal;">{{ $franchaisee->message }}</span></li>
        </ul>
    </div>
</body>

</html>