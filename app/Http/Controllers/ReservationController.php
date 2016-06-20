<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Http\Requests;
use App\Reservation;
use App\ReservationNight;
use App\RoomCalendar;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function createReservation(Request $request)
    {

        $room_info = $request['room_info'];

        $start_dt = Carbon::createFromFormat('d-m-Y', $request['start_dt'])->toDateString();
        $end_dt = Carbon::createFromFormat('d-m-Y', $request['end_dt'])->toDateString();

        $customer = Customer::firstOrCreate($request['customer']);

        $reservation = Reservation::create();
        $reservation->total_price = $room_info['total_price'];
        $reservation->occupancy = $request['occupancy'];
        $reservation->customer_id = $customer->id;
        $reservation->checkin = $start_dt;
        $reservation->checkout = $end_dt;

        $reservation->save();

        $date = $start_dt;

        while (strtotime($date) <= strtotime($end_dt)) {

            $room_calendar = RoomCalendar::where('day', '=', $date)
                ->where('room_type_id', '=', $room_info['id'])->first();

            $night = ReservationNight::create();
            $night->day = $date;

            $night->rate = $room_calendar->rate;
            $night->room_type_id = $room_info['id'];
            $night->reservation_id = $reservation->id;

            $room_calendar->availability--;
            $room_calendar->reservations++;

            $room_calendar->save();
            $night->save();

            $date = date("Y-m-d", strtotime("+1 day", strtotime($date)));

        }

        $nights = $reservation->nights;
        $customer = $reservation->customer;

        return $reservation;

    }

    public function getTodayReservation()
    {

        $room_calendar = Reservation::leftJoin('customers', 'reservations.customer_id', '=', 'customers.id')
            ->where("reservations.checkout", '=', date('Y-m-d'))
            ->where("reservations.confirm_checkout", '=', 0)
            ->select('customers.first_name', 'reservations.customer_id', 'customers.last_name', 'customers.email', 'reservations.total_price', 'reservations.checkout', 'reservations.id')->get();

        return $room_calendar;

    }

    public function getCheckout(Request $request)
    {
//        dd($request['reservations_id']);

        $reservation = Reservation::FindorFail($request['reservations_id']);
        $reservation->confirm_checkout = 1;
        $reservation->save();

//        print_r($request['reservations_id']);
        $reservation_night = ReservationNight::where("reservation_id", "=", $request['reservations_id'])->first();

//            dd($reservation_night->room_type_id);
        $room_calender = RoomCalendar::where("room_type_id", "=", $reservation_night->room_type_id)->first();

        $room_calender->availability++;
        $room_calender->reservations--;

        $room_calender->save();


    }
}

