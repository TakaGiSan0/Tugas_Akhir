<?php

namespace App\Exports;

use App\Models\training_record;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TrainingRecordExport implements FromCollection, WithHeadings
{
    protected $year;

    public function __construct($year)
    {
        $this->year = $year;
    }
    /**
     * Fungsi untuk mengambil data yang akan diekspor.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $data = Training_Record::with(['hasil_Peserta.pesertas', 'trainingCategory', 'training_skill_record.training_skill'])->orderBy('date_start', 'desc');

        if ($this->year !== 'all') {
            $data->whereYear('date_end', $this->year);
        }

        $data = $data->get()->flatMap(function ($record) {
            return $record->hasil_Peserta->map(function ($hasil) use ($record) {
                $skillCodes = $record->training_skill_record->pluck('training_skill.skill_code')->filter()->unique()->implode(', ');
                $jobSkills = $record->training_skill_record->pluck('training_skill.job_skill')->filter()->unique()->implode(', ');

                return [
                    'No' => 0,
                    'doc_ref' => $record->doc_ref,
                    'rev' => $record->rev,
                    'training_name' => $record->training_name,
                    'station' => $record->station,
                    'job_skill' => $jobSkills ?? '-',
                    'skill_code' => $skillCodes ?? '-',
                    'badge_no' => $hasil->pesertas->badge_no ?? '-',
                    'employee_name' => $hasil->pesertas->employee_name ?? '-',
                    'dept' => $hasil->pesertas->dept ?? '-',
                    'position' => $hasil->pesertas->position ?? '-',
                    'Training Start' => $record->date_start_formatted,
                    'Training End' => $record->date_end_formatted,
                    'trainer_name' => $record->trainer_name,
                    'training_duration' => $record->date_duration_formatted,
                    'theory_result' => $hasil->theory_result,
                    'practical_result' => $hasil->practical_result,
                    'level' => $hasil->level,
                    'final_judgement' => $hasil->final_judgement,
                    'category_name' => $record->trainingCategory->name ?? '-',
                    'license' => $hasil->license == 1 ? '☑' : '☐',
                ];
            });
        });

        $data = $data->map(function ($item, $key) {
            $item['No'] = $key + 1;
            return $item;
        });

        return collect($data);
    }

    /**
     * Fungsi untuk menambahkan header di Excel.
     *
     * @return array
     */
    public function headings(): array
    {
        return ['No', 'DOC. REF', 'REV', 'Training Name', 'Station', 'Job Skill', 'Skill Code', 'Badge No', 'Emp Name', 'Dept', 'Position', 'Training Start', 'Training End', 'Trainer Name','Training Duration', 'Theory Result', 'Practical Result', 'Level', 'Final Judgement', 'Category', 'License'];
    }

    private function formatTrainingDate($start, $end)
    {
        $start = \Carbon\Carbon::parse($start);
        $end = \Carbon\Carbon::parse($end);

        $startDay = $start->format('j');
        $startMonth = strtolower($start->format('M'));
        $startYear = $start->format('y');

        $endDay = $end->format('j');
        $endMonth = strtolower($end->format('M'));
        $endYear = $end->format('y');

        if ($start->equalTo($end)) {
            return "{$startDay} {$startMonth} {$startYear}";
        }

        if ($start->format('M Y') === $end->format('M Y')) {
            return "{$startDay}-{$endDay} {$startMonth} {$startYear}";
        }

        return "{$startDay} {$startMonth} - {$endDay} {$endMonth} {$endYear}";
    }
}
