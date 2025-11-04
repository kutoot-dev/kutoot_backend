<?php

namespace App\Exports;

use App\Models\CountryState;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CountryStateExport implements FromCollection , WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $is_dummy = false;

    public function __construct($is_dummy)
    {
        $this->is_dummy = $is_dummy;
    }


    public function headings(): array
    {
        return
            $this->is_dummy ? [
                'Country Id',
                'State Name',
                'State Slug',
                'Status',
            ] :
            [
                'Id',
                'Country Id',
                'State Name',
                'State Slug',
                'Status',
            ]
            ;
    }


    public function collection()
    {
        return $this->is_dummy ? CountryState::select('country_id','name','slug','status')->get() : CountryState::select('id','country_id','name','slug','status')->get();
    }
}
