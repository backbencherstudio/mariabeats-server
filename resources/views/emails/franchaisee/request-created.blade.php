@component('mail::message')

# Hello Gulf Franchisee Hub,

New Franchaisee Request from {{ $franchaisee->name }}.

Here are the details of the new franchaisee request:
- Name: {{ $franchaisee->name }}
- Email: {{ $franchaisee->email }}
- Phone: {{ $franchaisee->phone_number }}
- Country: {{ $franchaisee->country }}
- Preferred Location: {{ $franchaisee->preferred_location }}
- Investment Amount: {{ $franchaisee->investment_amount }}
- Timeframe: {{ $franchaisee->timeframe }}
- Message: {{ $franchaisee->message }}

Gulf Franchisee Hub
@endcomponent   