<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{

    private $employees;
    private $scores;
    private $rank=1;

    public function __construct(){
        $this->employees = collect([
            [
                'name' => 'John',
                'email' => 'john3@example.com',
                'sales' => [
                    ['customer' => 'The Blue Rabbit Company', 'order_total' => 7444],
                    ['customer' => 'Black Melon', 'order_total' => 1445],
                    ['customer' => 'Foggy Toaster', 'order_total' => 700],
                ],
            ],
            [
                'name' => 'Jane',
                'email' => 'jane8@example.com',
                'sales' => [
                    ['customer' => 'The Grey Apple Company', 'order_total' => 203],
                    ['customer' => 'Yellow Cake', 'order_total' => 8730],
                    ['customer' => 'The Piping Bull Company', 'order_total' => 3337],
                    ['customer' => 'The Cloudy Dog Company', 'order_total' => 5310],
                ],
            ],
            [
                'name' => 'Dave',
                'email' => 'dave1@example.com',
                'sales' => [
                    ['customer' => 'The Acute Toaster Company', 'order_total' => 1091],
                    ['customer' => 'Green Mobile', 'order_total' => 2370],
                ],
            ],
        ]);

        $this->scores = collect ([
            ['score' => 76, 'team' => 'A'],
            ['score' => 62, 'team' => 'B'],
            ['score' => 82, 'team' => 'C'],
            ['score' => 86, 'team' => 'D'],
            ['score' => 91, 'team' => 'E'],
            ['score' => 67, 'team' => 'F'],
            ['score' => 67, 'team' => 'G'],
            ['score' => 82, 'team' => 'H'],
        ]);
    }

    public function taskOutput(){
        return view('index',[
            "task1" => $this->filter(),
           "task2a" => $this->getCustomerOrderedMore(),
            "task2b" => $this->getCustomerSpentMore(),
            "task3" => $this->getRanks()
        ]);
    }


    // Employee who made the most valuable sale.

    public function filter(){
        $collection = $this->employees;

        $collection_ = $collection
            ->map(function($elems){
                return collect($elems['sales'])->max('order_total');
            })->sort()
            ->reverse()
            ->keys()
            ->first();

        return $collection->get($collection_)['name'];

    }

    // Find the first customer who spent more money on orders.
    public function getCustomerSpentMore(){
        $result = Payment::with('customer')->addSelect(DB::raw('sum(payments.amount) as total_payments,
        payments.customerNumber'))
            ->groupBy('payments.customerNumber')->limit(1)
            ->orderBy('total_payments', 'DESC')->first();

        return $result;
    }

    // Find the first customer who has highest number of orders.
    public function getCustomerOrderedMore(){
        $result = Order::with('customer')->addSelect(DB::raw('Count(orders.customerNumber) as total_orders,
        orders.customerNumber'))
            ->groupBy('orders.customerNumber')->limit(1)
            ->orderBy('total_orders', 'DESC')->first();

        return $result;
    }

    // Calculate ranks for the given teams.
    public function getRanks(){
        $collection = $this->scores;
       $collection = $collection
       ->sortByDesc('score')
       ->map(function($elem){
           return collect($elem)->put("rank",$this->rank++);
       });

       return $collection;

        // $collection = $this->scores;
        // $rankings = $collection
        //     ->groupBy('score')
        //     ->map(function ($group) {
        //         return $group->pluck('team');
        //     });

        // $rankings_sorted = $rankings->sortKeysDesc();

        // return $rankings_sorted->map(function($elem){
        //     return collect($elem)->put("rank",$this->rank++);
        // });
    }
}
