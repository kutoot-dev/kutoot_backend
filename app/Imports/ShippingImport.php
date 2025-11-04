<?php

namespace App\Imports;

use App\Models\Shipping;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ShippingImport implements ToModel , WithStartRow
{
    public function startRow(): int
    {
        return 2;
    }

    public function model(array $row)
    {
        return new Shipping([
            'city_id' => $row[0],
            'shipping_rule' => $row[1],
            'type' => $row[2],
            'shipping_fee' => $row[3],
            'condition_from' => $row[4],
            'condition_to' => $row[5]
        ]);
    }
}
