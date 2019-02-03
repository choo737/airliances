<?php

namespace App\Http\Controllers;

use App\Area;
use App\Booking;
use Auth;
use Illuminate\Http\Request;

class BookingController extends Controller
{
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('bookings/index')->with('bookings', Auth::user()->bookings);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $layout = 'layouts.app';
        $areas = Area::all();
        if (Auth::check()) {
            $layout = 'layouts.dashboard';
        }
        return view('bookings/create', compact('layout', 'areas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'area' => 'required|integer|min:0',
            'headcount' => 'required|integer|min:1',
            'roomcount' => 'required|integer|min:1',
            'dates' => 'required',
            'stars' => 'required|integer|min:2|max:5',
        ],
        [
            'area.min' => 'Invalid area',
            'headcount' => 'Invalid number of travellers',
            'roomcount' => 'Invalid number of rooms',
            'stars' => 'Invalid number of stars',
            'dates' => 'Date is required'
        ]);
        
        $booking = new Booking([
            'user_id' => Auth::user()->id,
            'area_id' => $request->get('area'),
            'headcount' => $request->get('headcount'),
            'roomcount' => $request->get('roomcount'),
            'stars' => $request->get('stars'),
            'date_from' => substr($request->get('dates'), 0, 10),
            'date_to' => substr($request->get('dates'), strlen($request->get('dates')) - 10, 10)
        ]);
        //dd($booking);
        $booking->save();
        return redirect('/mybookings')->with('success', 'booking has been made');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function show(Booking $booking)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function edit(Booking $booking)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Booking $booking)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function destroy(Booking $booking)
    {
        //
    }
}
