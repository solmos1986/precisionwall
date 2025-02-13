<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\HasReferencesToOtherSheets;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class Import_to_text implements ToCollection, WithCalculatedFormulas, HasReferencesToOtherSheets
{
    public function collection(Collection $rows)
    {
        return $row;
    }

}
/* use Maatwebsite\Excel\Concerns\HasReferencesToOtherSheets;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class Import_to_text implements
ToArray, WithCalculatedFormulas, HasReferencesToOtherSheets
{

private $data;

public function __construct()
{
$this->data = [];
}
function array(array $rows) {
foreach ($rows as $key => $row) {
$this->data[] = $row;
}
}

public function getArray()
{
return $this->data;
}
}
 */
