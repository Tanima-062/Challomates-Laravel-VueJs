<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $payment_methods = [
            [
                'name'  =>  'Mastercard',
                'logo'  =>  'images/payment_medhods/master_card.png'
            ],
            [
                'name'  =>  'American Express',
                'logo'  =>  'images/payment_medhods/american_express.png'
            ],
            [
                'name'  =>  'Visa',
                'logo'  =>  'images/payment_medhods/visa.png'
            ],
            [
                'name'  =>  'Diners',
                'logo'  =>  'images/payment_medhods/dinners_club.png'
            ],
            [
                'name'  =>  'Post',
                'logo'  =>  'images/payment_medhods/swiss_post.png'
            ],
            [
                'name'  =>  'PayPal',
                'logo'  =>  'images/payment_medhods/paypal.png'
            ],
            [
                'name'  =>  'Mastercard',
                'logo'  =>  'images/payment_medhods/twint.png'
            ],
            [
                'name'  =>  'Apple Pay',
                'logo'  =>  'images/payment_medhods/apple_pay.png'
            ],
            [
                'name'  =>  'Samsung Pay',
                'logo'  =>  'images/payment_medhods/samsung_pay.png'
            ],
        ];

        DB::table('payment_methods')->insert($payment_methods);
    }
}
