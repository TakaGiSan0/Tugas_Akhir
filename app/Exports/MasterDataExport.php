<?php

namespace App\Exports;

use App\Models\Peserta;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MasterDataExport implements FromCollection , WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Peserta::select('badge_no', 'employee_name', 'join_date', 'dept', 'position', 'category_level', 'gender')
        ->orderBy('employee_name', 'asc')
        ->get();
    }

    /** 
     * Fungsi untuk menambahkan header di Excel.
     * 
     * @return array
     */
    public function headings(): array
    {
        return [
            'Badge No',
            'Employee Name',
            'Join Date',
            'Dept',
            'Position',
            'Level Category', 
            'Gender',
        ];
    }


    
}
