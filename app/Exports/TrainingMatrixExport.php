<?php

namespace App\Exports;

use App\Models\Training_Record;
use App\Models\Peserta;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Ramsey\Uuid\Codec\OrderedTimeCodec;

class TrainingMatrixExport implements FromArray, WithHeadings
{
    private $pesertas;
    private $allStations;
    private $allSkillCodes;

    public function __construct()
    {
        $this->pesertas = Peserta::whereHas('trainingRecords', function ($query) {
            $query->where('status', 'completed');
        })->orderby('employee_name', 'asc')
        ->get();

        $this->allStations = Training_Record::where('status', 'completed')
            ->pluck('station')->unique()->toArray();

        $this->allSkillCodes = Training_Record::where('status', 'completed')
            ->pluck('skill_code')->flatMap(fn($s) => explode(', ', $s))->unique()->toArray();
    }

    public function headings(): array
    {
        return array_merge([
            ['Badge No', 'Employee Name', 'Department', 'Join Date', 'Station', ...array_fill(0, count($this->allStations), ''), 'Skill Code', ...array_fill(0, count($this->allSkillCodes), '')],
            ['', '', '', '', ...$this->allStations, ...$this->allSkillCodes]
        ]);
        
    }

    public function array(): array
    {
        $data = [];
        $stationsWithSupply = array_fill_keys($this->allStations, 0);

        foreach ($this->pesertas as $peserta) {
            $row = [
                $peserta->badge_no,
                $peserta->employee_name,
                $peserta->dept,
                $peserta->join_date,
            ];

            foreach ($this->allStations as $station) {
                $levels = $peserta->trainingRecords
                    ->filter(fn($training) => in_array($station, explode(', ', $training->station)))
                    ->pluck('pivot.level')
                    ->toArray();

                $angkaLevels = array_filter($levels, fn($level) => is_numeric($level));
                $naLevels = array_filter($levels, fn($level) => strtoupper($level) === 'NA');

                $levelTertinggi = !empty($angkaLevels) ? max($angkaLevels) : (!empty($naLevels) ? 'NA' : '-');

                if (in_array($levelTertinggi, ['3', '4'])) {
                    $stationsWithSupply[$station]++;
                }

                $row[] = $levelTertinggi;
            }

            foreach ($this->allSkillCodes as $skill) {
                $hasTraining = $peserta->trainingRecords->contains(fn($training) => str_contains($training->skill_code, $skill));
                $row[] = $hasTraining ? '✔' : '-';
            }

            $data[] = $row;
        }

        $totalParticipants = count($this->pesertas);

        $supplyRow = array_merge(
            ['Supply', '', '', ''],
            array_map(fn($supply) => $supply, $stationsWithSupply),
            array_fill(0, count($this->allSkillCodes), '') 
        );

        $gapRow = array_merge(
            ['Gap', '', '', ''],
            array_map(fn($supply) => $totalParticipants - $supply, $stationsWithSupply),
            array_fill(0, count($this->allSkillCodes), '')
        );

        $data[] = $supplyRow;
        $data[] = $gapRow;

        return $data;
    }
}
