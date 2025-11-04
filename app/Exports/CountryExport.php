<?php

namespace App\Exports;

use App\Models\Country;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
class CountryExport implements FromCollection , WithHeadings
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
                'Name',
                'Slug',
                'Status',
            ] :
            [
                'Id',
                'Name',
                'Slug',
                'Status',
            ]
            ;
    }

    public function collection()
    {
        return $this->is_dummy ? Country::select('name','slug','status')->get() : Country::select('id','name','slug','status')->get();
    }
}
