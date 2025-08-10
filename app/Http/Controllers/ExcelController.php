<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\TrainingRecordsImport;
use App\Imports\MasterDataImport;
use App\Exports\MasterDataExport;
use App\Exports\TrainingRecordExport;
use App\Exports\TrainingMatrixExport;
use App\Exports\MatrixExport;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    public function import_peserta(Request $request)
    {
       
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $userId = auth('')->id();

 
        Excel::import(new MasterDataImport($userId), $request->file('file'));

        return redirect()->back()->with('success', 'Import Master Data Successfully');
    }

    public function export_peserta()
    {
        $date = date('Y-m-d'); 
        $fileName = 'Master Data - ' . $date . '.xlsx';
        return Excel::download(new MasterDataExport(), $fileName);
    }

    public function import_training(Request $request)
    {

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);



        Excel::import(new TrainingRecordsImport, $request->file('file'));

        return redirect()->back()->with('success', 'Import Training successfully ');
    }

    public function export_training(request $request)
    {
        $year = $request->get('year', 'all');
        $date = date('Y-m-d'); 
        $fileName = 'Training Record - ' . $date . '.xlsx';
        return Excel::download(new TrainingRecordExport($year), $fileName);
    }

    public function export_matrix()
    {

        $date = date('Y-m-d'); 
        $fileName = 'Matrix - ' . $date . '.xlsx';
        return Excel::download(new MatrixExport(), $fileName);
    }

    public function export_training_matrix(request $request)
    {
        $date = date('Y-m-d'); 
        $fileName = 'Training Matrix - ' . $date . '.xlsx';
        return Excel::download(new TrainingMatrixExport(), $fileName);
    }

}
