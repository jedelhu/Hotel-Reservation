<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Http\Requests;
use App\Reservation;
use App\ReservationNight;
use App\RoomCalendar;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Anouar\Paypalpayment\Facades\PaypalPayment;

class ReservationController extends Controller
{

    public function __construct()
    {

        // ### Api Context
        // Pass in a `ApiContext` object to authenticate
        // the call. You can also send a unique request id
        // (that ensures idempotency). The SDK generates
        // a request id if you do not pass one explicitly.

        $this->_apiContext = Paypalpayment::ApiContext(config('paypal_payment.Account.ClientId'), config('paypal_payment.Account.ClientSecret'));

        // Uncomment this step if you want to use per request
        // dynamic configuration instead of using sdk_config.ini

        $config = config('paypal_payment'); // Get all config items as multi dimensional array
        $flatConfig = array_dot($config); // Flatten the array with dots

        $this->_apiContext->setConfig($flatConfig);
    }

    public function createReservation(Request $request)
    {

        $room_info = $request['room_info'];

        $start_dt = Carbon::createFromFormat('d-m-Y', $request['start_dt'])->toDateString();
        $end_dt = Carbon::createFromFormat('d-m-Y', $request['end_dt'])->toDateString();

        $customer = Customer::firstOrCreate($request['customer']);
        $this->store($request['customer']);

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

    /*
  * Display form to process payment using credit card
  */
    public function create()
    {
        return View::make('payment.order');
    }

    /*
    * Process payment using credit card
    */
    public function store($inputvalues)
    {

        $name=explode(" ",$inputvalues["card_name"]);
        $expiry=explode("/",$inputvalues["card_expiry"]);
        // ### Address
        // Base Address object used as shipping or billing
        // address in a payment. [Optional]
        $addr= Paypalpayment::address();
        $addr->setLine1("3909 Witmer Road");
        $addr->setLine2("Niagara Falls");
        $addr->setCity("Niagara Falls");
        $addr->setState("NY");
        $addr->setPostalCode("14305");
        $addr->setCountryCode("US");
        $addr->setPhone("716-298-1822");

        // ### CreditCard
        $card = Paypalpayment::creditCard();
        $card->setType("visa")
            ->setNumber($inputvalues["card_number"])
            ->setExpireMonth($expiry[0])
            ->setExpireYear($expiry[1])
            ->setCvv2($inputvalues["cvc"])
            ->setFirstName($name[0])
            ->setLastName($name[1]);

//        dd($card);

//        $card->setType("visa")
//            ->setNumber("4758411877817150")
//            ->setExpireMonth("05")
//            ->setExpireYear("2019")
//            ->setCvv2("456")
//            ->setFirstName("Joe")
//            ->setLastName("Shopper");

        // ### FundingInstrument
        // A resource representing a Payer's funding instrument.
        // Use a Payer ID (A unique identifier of the payer generated
        // and provided by the facilitator. This is required when
        // creating or using a tokenized funding instrument)
        // and the `CreditCardDetails`
        $fi = Paypalpayment::fundingInstrument();
        $fi->setCreditCard($card);

        // ### Payer
        // A resource representing a Payer that funds a payment
        // Use the List of `FundingInstrument` and the Payment Method
        // as 'credit_card'
        $payer = Paypalpayment::payer();
        $payer->setPaymentMethod("credit_card")
            ->setFundingInstruments(array($fi));

        $item1 = Paypalpayment::item();
        $item1->setName('Ground Coffee 40 oz')
            ->setDescription('Ground Coffee 40 oz')
            ->setCurrency('USD')
            ->setQuantity(1)
            ->setTax(0.3)
            ->setPrice(7.50);

        $item2 = Paypalpayment::item();
        $item2->setName('Granola bars')
            ->setDescription('Granola Bars with Peanuts')
            ->setCurrency('USD')
            ->setQuantity(5)
            ->setTax(0.2)
            ->setPrice(2);


        $itemList = Paypalpayment::itemList();
        $itemList->setItems(array($item1,$item2));


        $details = Paypalpayment::details();
        $details->setShipping("1.2")
            ->setTax("1.3")
            //total of items prices
            ->setSubtotal("17.5");

        //Payment Amount
        $amount = Paypalpayment::amount();
        $amount->setCurrency("USD")
            // the total is $17.8 = (16 + 0.6) * 1 ( of quantity) + 1.2 ( of Shipping).
            ->setTotal("20")
            ->setDetails($details);

        // ### Transaction
        // A transaction defines the contract of a
        // payment - what is the payment for and who
        // is fulfilling it. Transaction is created with
        // a `Payee` and `Amount` types

        $transaction = Paypalpayment::transaction();
        $transaction->setAmount($amount)
            ->setItemList($itemList)
            ->setDescription("Payment description")
            ->setInvoiceNumber(uniqid());

        // ### Payment
        // A Payment Resource; create one using
        // the above types and intent as 'sale'

        $payment = Paypalpayment::payment();

        $payment->setIntent("sale")
            ->setPayer($payer)
            ->setTransactions(array($transaction));

        try {
            // ### Create Payment
            // Create a payment by posting to the APIService
            // using a valid ApiContext
            // The return object contains the status;
            $payment->create($this->_apiContext);
        } catch (\PPConnectionException $ex) {
            return  "Exception: " . $ex->getMessage() . PHP_EOL;
            exit(1);
        }

//        dd($payment);
//        return true;
    }


    public function getTodayReservation()
    {

        $room_calendar = Reservation::leftJoin('customers', 'reservations.customer_id', '=', 'customers.id')
            ->leftJoin('orders', 'customers.id', '=', 'orders.customer_id')
            ->where("reservations.checkout", '=', date('Y-m-d'))
            ->where("reservations.confirm_checkout", '=', 0)
            ->select('customers.first_name', 'reservations.customer_id', 'customers.last_name', 'customers.email', 'reservations.total_price', 'reservations.checkout', 'reservations.id','orders.price')->get();

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

