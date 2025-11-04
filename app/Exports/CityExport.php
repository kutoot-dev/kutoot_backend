<?php

namespace App\Exports;

use App\Models\City;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CityExport implements FromCollection , WithHeadings
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
                'State Id',
                'Name',
                'Slug',
                'Status',
            ] :
            [
                'Id',
                'State Id',
                'Name',
                'Slug',
                'Status',
            ]
            ;
    }


    public function collection()
    {
        return $this->is_dummy ? City::select('country_state_id','name','slug','status')->get() : City::select('id','country_state_id','name','slug','status')->get();
    }
}
