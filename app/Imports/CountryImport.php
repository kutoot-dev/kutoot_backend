<?php

namespace App\Imports;

use App\Models\Country;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class CountryImport implements ToModel , WithStartRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

    public function startRow(): int
    {
        return 2;
    }


    public function model(array $row)
    {
        return new Country([
            'name' => $row[0],
            'slug' => $row[1],
            'status' => $row[2],
        ]);
    }
}
