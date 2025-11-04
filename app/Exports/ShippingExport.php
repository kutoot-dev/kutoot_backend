<?php

namespace App\Exports;

use App\Models\Shipping;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ShippingExport implements FromCollection , WithHeadings
{
    protected $is_dummy = false;

    public function __construct($is_dummy)
    {
        $this->is_dummy = $is_dummy;
    }


    public function headings(): array
    {
        return
            $this->is_dummy ? [
                'City Id',
                'Shipping Rule',
                'Type',
                'Shipping Fee',
                'Condition From',
                'Condition To'
            ] :
            [
                'Id',
                'City Id',
                'Shipping Rule',
                'Type',
                'Shipping Fee',
                'Condition From',
                'Condition To'
            ]
            ;
    }

    public function collection()
    {
        return $this->is_dummy ? Shipping::select('city_id','shipping_rule','type','shipping_fee','condition_from','condition_to')->get() : Shipping::select('id','city_id','shipping_rule','type','shipping_fee','condition_from','condition_to')->get();
    }
}
