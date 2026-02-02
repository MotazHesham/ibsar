<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class BeneficiaryOrderImport implements ToArray, WithHeadingRow, WithCalculatedFormulas
{
    public function array(array $rows): array
    {
        return array_map(function ($row) {
            return array_map(function ($value) {
                // Handle empty/null
                if ($value === null) {
                    return null;
                } 

                // Otherwise force string
                return (string) $value;
            }, $row);
        }, $rows);
    }
}
