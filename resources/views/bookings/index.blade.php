@extends('layouts.dashboard') 
@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            My Bookings
        </div>
        @foreach ($bookings as $booking)
        <div class="card-body">
            <h5 class="card-title">{{ $booking->area->name }}</h5>
            <p class="card-text">
                Travel Date: {{ $booking->date_from }} - {{ $booking->date_to }}<br>
                Number of Travellers: {{ $booking->headcount }}<br>
                Number of Rooms: {{ $booking->roomcount }}
            </p>
        </div>
        @endforeach
    </div>
</div>
@endsection