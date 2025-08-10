<?php

namespace App\Imports;

use App\Models\Training_Record;
use App\Models\Peserta;
use App\Models\Category;
use App\Models\Hasil_Peserta;
use App\Models\training_comment;
use App\Models\training_skill;
use App\Models\trainingskillrecord;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;
use DateTime;

class TrainingRecordsImport implements ToModel, WithHeadingRow
{

    public function startRow(): int
    {
        return 2;
    }

    /**
     * Fungsi model untuk mengimpor data.
     * 
     * @param array $row
     * 
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {

        if (!empty($row['training_category'])) {
            $category = Category::firstOrCreate(['name' => $row['training_category']], ['id' => $this->getCategoryId($row['training_category'])]);
        } else {
            $category = Category::firstOrCreate(['name' => 'N/A']);
        }

        if (is_numeric($row['training_start'])) {
            $formattedDateStart = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['training_start'])->format('Y-m-d');
        } else {
            $dates = $this->parseDateRange($row['training_start']);
            $formattedDateStart = $dates['start'];
        }
        
        if (is_numeric($row['training_end'])) {
            $formattedDateEnd = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['training_end'])->format('Y-m-d');
        } else {
            $dates = $this->parseDateRange($row['training_end']);
            $formattedDateEnd = $dates['end'];
        }
        
        $minutes = (int) $row['training_duration_minute'];
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        $durationTime = sprintf('%02d:%02d:00', $hours, $mins);

        $peserta = Peserta::where('badge_no', $row["badge_no"])->first();

        if (!$peserta) {
            return; 
        }

        $trainingRecord = Training_Record::firstOrCreate([
            'doc_ref' => $row['doc_ref'],
            'rev' => $row['rev'],
            'training_name' => $row['training_name'],
            'station' => $row['station'],
            'date_start' => $formattedDateStart,
            'date_end' => $formattedDateEnd,
            'training_duration'=> $durationTime,
            'trainer_name' => $row['trainer_name'],
            'category_id' => $category->id,
            'status' => 'Completed',
            'user_id' => auth('web')->id(), 

        ]);
        Hasil_Peserta::firstOrCreate([
            'training_record_id' => $trainingRecord->id,
            'peserta_id' => $peserta->id,
            'theory_result' => $row['theory_result'],
            'practical_result' => $row['practical_result'],
            'level' => $row['level'],
            'final_judgement' => $row['final_judgement'],
            'license' => ($row['lisence_certification'] == '√' ? '1' : '0'),
        ]);

        training_comment::firstOrCreate([
            'training_record_id' => $trainingRecord->id,
            'approval' => 'Approved',
        ]);

        $skillCodes = array_map('trim', explode(',', $row['skill_code']));
        $jobSkills  = array_map('trim', explode(',', $row['job_skill']));

        if (count($skillCodes) !== count($jobSkills)) {
            Log::warning("Jumlah skill_code dan job_skill tidak cocok di baris: ", $row);
            return;
        }

        $seenCodes = []; 

        foreach ($skillCodes as $i => $code) {
            if (in_array($code, $seenCodes)) {
                continue;
            }
            $seenCodes[] = $code;

            $existing = training_skill::where('skill_code', $code)->first();

            if ($existing) {
                $trainingskill = $existing;
            } else {
                $trainingskill = training_skill::create([
                    'skill_code' => $code,
                    'job_skill' => $jobSkills[$i],
                ]);
            }

            
            trainingskillrecord::firstOrCreate([
                'training_record_id' => $trainingRecord->id,
                'training_skill_id' => $trainingskill->id,
            ]);
        }
    }

    /**
     * Dapatkan category_id berdasarkan nama kategori.
     *
     * @param string $categoryName
     * @return int
     */
    private function getCategoryId($categoryName)
    {
        switch (strtolower($categoryName)) {
            case 'NEO':
                return 1;  
            case 'PROJECT':
                return 2;
            case 'INTERNAL':
                return 3;
            case 'EXTERNAL':
                return 4;
            default:
                return Category::firstOrCreate(['name' => $categoryName])->id;  
        }
    }


    private function parseDateRange($dateRange)
    {

        preg_match('/(\d{2,4})$/', $dateRange, $yearMatch);

        $year = $yearMatch[1] ?? date('Y');

        if (strlen($year) == 2) {
            $year = (intval($year) > 30) ? "19$year" : "20$year";
        }

        $dateRange = str_replace($yearMatch[0], '', $dateRange);

        $parts = preg_split('/[-–]/', $dateRange);
        $startPart = trim($parts[0] ?? '');
        $endPart = trim($parts[1] ?? $startPart);

        preg_match('/[A-Za-z]{3,}/', $endPart, $monthMatch);
        $month = $monthMatch[0] ?? '';

        if (!$month) {
            preg_match('/[A-Za-z]{3,}/', $startPart, $monthMatch);
            $month = $monthMatch[0] ?? '';
        }

        preg_match('/d{1,2}/', $startPart, $dayStartMatch);
        preg_match('/\d{1,2}/', $endPart, $dayEndMatch);

        $dayStart = $dayStartMatch[0] ?? '01';
        $dayEnd = $dayEndMatch[0] ?? '01';

        $startMonth = $month;
        if ((int)$dayStart > 28 && (int)$dayEnd <= 12) {
            $startMonth = date('M', strtotime("-1 month", strtotime("1 $month $year")));
        }

        $startDate = DateTime::createFromFormat('j M Y', "$dayStart $startMonth $year");

        $endDate = DateTime::createFromFormat('j M Y', "$dayEnd $month $year");

        return [
            'start' => $startDate ? $startDate->format('Y-m-d') : '1970-01-01',
            'end' => $endDate ? $endDate->format('Y-m-d') : '1970-01-01',
        ];
    }



    private function getPreviousMonthAbbreviation($monthAbbrev)
    {
        try {
            $date = DateTime::createFromFormat('M', $monthAbbrev);
            $date->modify('-1 month');
            return $date->format('M'); 
        } catch (\Exception $e) {
            return $monthAbbrev;
        }
    }
}
