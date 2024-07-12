<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Rules\ListOfPositiveNumbersRule;
use Carbon\Carbon;
use Illuminate\Http\Request;

class QuotationController extends Controller
{
    public function getQuotations(Request $request)
    {
        $request->validate([
            'limit' => 'integer|min:1|max:100',
            'page' => 'integer|min:1',
            'sortBy' => 'in:id,age,currency_id,start_date,end_date,total_price',
            'sortOrder' => 'in:asc,desc',
        ]);
        $limit = $request->input('limit', 10);
        $page = $request->input('page', 1);
        $sortBy = $request->input('sortBy', 'created_at');
        $sortOrder = $request->input('sortOrder', 'desc');

        $quotations = Quotation::query()
            ->orderBy($sortBy, $sortOrder)
            ->paginate($limit, ['*'], 'page', $page);

        return response()->json($quotations);
    }

    public function createQuotation(Request $request)
    {
        $request->validate([
            'age' => ['required', new ListOfPositiveNumbersRule],
            'currency_id' => 'required|in:EUR,GBP,USD',
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
        ]);

        // validate that age is a number or a comma-separated list of numbers


        $ages = explode(',', $request->input('age'));

        $startDate = Carbon::parse($request->input('start_date'));
        $endDate = Carbon::parse($request->input('end_date'));
        $tripLength = $startDate->diffInDays($endDate) + 1;

        $total = 0;
        foreach ($ages as $age) {
            $ageLoad = $this->getAgeLoad($age);
            $total += 3 * $ageLoad * $tripLength;
        }

        $quotation = new Quotation();
        $quotation->user_id = auth('api')->user()->id;
        $quotation->age = $request->input('age');
        $quotation->currency_id = $request->input('currency_id');
        $quotation->start_date = $request->input('start_date');
        $quotation->end_date = $request->input('end_date');
        $quotation->total_price = $total;
        $quotation->save();

        return response()->json([
            'total' => number_format($total, 2, '.', ''),
            'currency_id' => $request->input('currency_id'),
            'quotation_id' => $quotation->id,
        ]);
    }

    private function getAgeLoad(int $age): float
    {
        if ($age < 18) {
            return 0.0;
        }

        if ($age >= 18 && $age <= 30) {
            return 0.6;
        }

        if ($age > 30 && $age <= 40) {
            return 0.7;
        }

        if ($age > 40 && $age <= 50) {
            return 0.8;
        }

        if ($age > 50 && $age <= 60) {
            return 0.9;
        }

        if ($age > 60 && $age <= 70) {
            return 1.0;
        }
        return 0.0;
    }
}
