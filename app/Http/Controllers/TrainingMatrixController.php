<?php

namespace App\Http\Controllers;

use App\Models\training_record;
use App\Models\peserta;
use App\Exports\TrainingMatrixExport;
use App\Models\Hasil_Peserta;
use App\Models\training_skill;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class TrainingMatrixController extends Controller
{

    public function index(Request $request)
    {


        $departments = Peserta::whereHas('trainingRecords', fn($q) => $q->where('status', 'completed'))
            ->distinct()->pluck('dept')->toArray();

        $masterSkills = Training_Skill::withTrashed()
            ->where('skill_code', '!=', 'NA')
            ->where('skill_code', '!=', 'N/A')
            ->whereNotNull('skill_code')
            ->where('skill_code', '!=', '')
            ->get()
            ->keyBy('skill_code');
        $allStations = Training_Record::where('status', 'Completed')
            ->pluck('station')
            ->flatMap(fn($s) => explode(', ', $s))
            ->unique()
            ->filter(function ($station) {
                $trimmedStation = trim($station);
                $upperStation = strtoupper($trimmedStation);
                return !empty($trimmedStation) && $upperStation !== 'NA' && $upperStation !== 'N/A';
            })
            ->sort()
            ->values();

        $user = auth('')->user();

        $pesertasQuery = Peserta::query()
            ->ByDept()
            ->with([
                'trainingRecords' => function ($query) {
                    $query->where('status', 'completed')
                        ->with('training_Skills');
                }
            ])
            ->whereHas('trainingRecords', fn($q) => $q->where('status', 'completed'))
            ->where(function ($query) {
                $query->whereHas('trainingRecords', function ($q) {
                    $q->where('status', 'Completed')
                        ->where(function ($subq) {
                            $subq->whereNotNull('status')
                                ->where('station', '!=', '')
                                ->whereRaw("UPPER(station) NOT IN ('NA', 'N/A')");
                        });
                })->orWhereHas("trainingRecords.training_Skills", function ($q) {
                    $q->whereNotNull('skill_code')
                        ->where('skill_code', '!=', '')
                        ->whereRaw("UPPER(station) NOT IN ('NA', 'N/A')");
                });
            })
            ->when($request->dept, fn($q) => $q->whereIn('dept', (array) $request->dept))
            ->when($request->searchQuery, fn($q) => $q->where(function ($subq) use ($request) {
                $subq->where('employee_name', 'like', "%{$request->searchQuery}%")
                    ->orWhere('badge_no', 'like', "%{$request->searchQuery}%");
            }))

            ->orderBy('employee_name');



        $pesertas = $pesertasQuery->select('id', 'badge_no', 'join_date', 'employee_name', 'dept')->paginate(10);
        $pesertaOnePage = $pesertas->count();

        $pesertas->through(function ($peserta) use ($allStations, $masterSkills) {

            $stationLevels = [];
            foreach ($peserta->trainingRecords as $record) {
                $levels = explode(', ', $record->pivot->level ?? '');
                $stationsInRecord = explode(', ', $record->station);
                foreach ($stationsInRecord as $index => $stationName) {
                    if (!isset($stationLevels[$stationName]))
                        $stationLevels[$stationName] = [];
                    $stationLevels[$stationName][] = $levels[$index] ?? null;
                }
            }

            $stationResults = [];
            foreach ($allStations as $station) {
                $levelsForStation = collect($stationLevels[$station] ?? [])->filter();
                $angkaLevels = $levelsForStation->filter(fn($l) => is_numeric($l))->all();
                $naLevels = $levelsForStation->filter(fn($l) => strtoupper($l) === 'NA')->isNotEmpty();
                $stationResults[$station] = !empty($angkaLevels) ? max($angkaLevels) : ($naLevels ? 'NA' : '-');
            }

            $ownedSkills = $peserta->trainingRecords->pluck('training_Skills')->flatten()->pluck('skill_code')->unique();
            $skillLookup = $ownedSkills->flip();

            $peserta->processed_stations = $stationResults;
            $peserta->processed_skills_lookup = $skillLookup;

            return $peserta;
        });

        $allPesertaIds = $pesertas->pluck('id');
        $stationsWithLevels = Hasil_Peserta::whereIn('peserta_id', $allPesertaIds)
            ->whereIn('level', ['3', '4'])
            ->join('training_records', 'hasil_peserta.training_record_id', '=', 'training_records.id')
            ->where('status', 'completed')
            ->groupBy('station')
            ->selectRaw('station, COUNT(DISTINCT peserta_id) as total')
            ->pluck('total', 'station')
            ->all();


        $stationsWithLevels = collect($allStations)
            ->mapWithKeys(
                fn($station) => [
                    $station => $stationsWithLevels[$station] ?? 0,
                ],
            )
            ->toArray();

        $stationsWithGaps = collect($stationsWithLevels)
            ->mapWithKeys(
                fn($count, $station) => [
                    $station => $pesertaOnePage - $count,
                ],
            )
            ->toArray();

        return view('content.training_matrix', [
            'pesertas' => $pesertas,
            'stationsWithLevels' => $stationsWithLevels,
            'stationsWithGaps' => $stationsWithGaps,
            'allStations' => $allStations,
            'masterSkills' => $masterSkills,
            'departments' => $departments,
            'searchQuery' => $request->searchQuery,
         


        ]);
    }

    public function downloadpdf(Request $request)
    {
        $dept = $request->input('dept');
        $deptList = is_array($dept) ? implode(', ', $dept) : $dept;

      

        $masterSkills = Training_Skill::withTrashed()
            ->where('skill_code', '!=', 'NA')
            ->where('skill_code', '!=', 'N/A')
            ->whereNotNull('skill_code')  
            ->where('skill_code', '!=', '')   
            ->get()
            ->keyBy('skill_code');

        $masterStations = Training_Record::where('status', 'Completed')
            ->pluck('station')
            ->flatMap(fn($s) => explode(', ', $s))
            ->unique()
            ->filter(function ($station) {
            
                $trimmedStation = trim($station);
                $upperStation = strtoupper($trimmedStation);
                return !empty($trimmedStation) && $upperStation !== 'NA' && $upperStation !== 'N/A';
            })
            ->sort()
            ->values();

        $allPeserta = Peserta::query()
            ->whereHas('trainingRecords', fn($q) => $q->where('status', 'completed'))
            ->when($dept, function ($query) use ($dept) {
                is_array($dept) ? $query->whereIn('dept', $dept) : $query->where('dept', $dept);
            })
            ->with([
             
                'trainingRecords' => function ($query) {
                    $query->where('status', 'completed')->with('training_Skills');
                }
            ])
            ->orderBy('employee_name', 'asc')
            ->get();

        $participantDataMap = [];
        foreach ($allPeserta as $peserta) {
           
            $ownedSkills = $peserta->trainingRecords->pluck('training_Skills')->flatten()->pluck('skill_code')->unique();
            $participantSkillLookup = $ownedSkills->flip();

            $stationLevels = [];
            foreach ($peserta->trainingRecords as $record) {
                $levels = explode(', ', $record->pivot->level ?? '');
                $stationsInRecord = explode(', ', $record->station);
                foreach ($stationsInRecord as $index => $stationName) {
                    if (!isset($stationLevels[$stationName]))
                        $stationLevels[$stationName] = [];
                    $stationLevels[$stationName][] = $levels[$index] ?? null;
                }
            }

            $stationResults = [];
            foreach ($masterStations as $station) {
                $levelsForStation = collect($stationLevels[$station] ?? [])->filter();
                $angkaLevels = $levelsForStation->filter(fn($l) => is_numeric($l))->all();
                $naLevels = $levelsForStation->filter(fn($l) => strtoupper($l) === 'NA')->isNotEmpty();
                $stationResults[$station] = !empty($angkaLevels) ? max($angkaLevels) : ($naLevels ? 'NA' : '-');
            }

            $participantDataMap[$peserta->id] = [
                'skills' => $participantSkillLookup,
                'stations' => $stationResults,
            ];
        }

        $results = [];
        $stationsWithSupply = array_fill_keys($masterStations->all(), 0);

        foreach ($allPeserta as $peserta) {
            $row = [
                'badge_no' => $peserta->badge_no,
                'employee_name' => $peserta->employee_name,
                'dept' => $peserta->dept,
                'join_date' => $peserta->join_date,
                'stations' => [],
                'skills' => [],
            ];
            $stationData = $participantDataMap[$peserta->id]['stations'];
            $skillDataLookup = $participantDataMap[$peserta->id]['skills'];

            foreach ($masterStations as $station) {
                $levelTertinggi = $stationData[$station] ?? '-';
                $row['stations'][$station] = $levelTertinggi;
                if (in_array($levelTertinggi, ['3', '4'])) {
                    $stationsWithSupply[$station]++;
                }
            }

            foreach ($masterSkills as $skillCode => $skillModel) {
                $row['skills'][$skillCode] = isset($skillDataLookup[$skillCode]) ? '✓' : '-';
            }

            $results[] = $row;
        }

        $totalParticipants = count($allPeserta);
        $supplyRow = $stationsWithSupply;
        $gapRow = array_map(fn($supply) => $totalParticipants - $supply, $stationsWithSupply);

        $pdf = Pdf::loadView('pdf.training_matrix', compact('results', 'masterStations', 'masterSkills', 'supplyRow', 'gapRow', 'deptList'))
            ->setPaper('A4', 'landscape');

        return $pdf->download('Training_Matrix.pdf');
    }
}
