<?php

namespace App\Http\Controllers;

use App\Jobs\FileJobs\ShiftPartialJob;
use Illuminate\Http\Request;

class ShiftPartialController extends FileJobController
{
    protected $indexRouteName = 'shiftPartial';

    protected $job = ShiftPartialJob::class;

    public function index()
    {
        return view('tools.partial-shifter');
    }

    protected function rules(): array
    {
        return [
            'shifts' => 'required|array',
            'shifts.*.from' => 'required|regex:/\d\d:\d\d:\d\d/',
            'shifts.*.to' => 'required|regex:/\d\d:\d\d:\d\d/',
            'shifts.*.milliseconds' => 'required|numeric|not_in:0|regex:/^(-?\d+)$/',
        ];
    }

    protected function options(Request $request)
    {
        $shifts = $request->get('shifts');

        foreach ($shifts as $shift) {
            $fromInt = str_replace(':', '', $shift['from']);
            $toInt = str_replace(':', '', $shift['to']);

            if ($fromInt >= $toInt) {
                return back()
                    ->withInput($request->input())
                    ->withErrors(['shifts' => __('validation.partial_shift_from_can_not_be_larger_than_shift_to')]);
            }
        }

        $shifts = array_map(function ($shift) {
            return collect($shift)->filter(function ($value, $key) {
                return in_array($key, ['from', 'to', 'milliseconds']);
            })->all();
        }, $shifts);

        return [
            'shifts' => $shifts,
        ];
    }
}
