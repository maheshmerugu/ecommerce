<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\State;
use App\Models\City;
use App\Models\Pincode;
use Illuminate\Support\Str;

class ImportLocationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Usage: php artisan locations:import --file=storage/imports/india_pincodes.csv
     */
    protected $signature = 'locations:import {--file= : Path to CSV file}';

    /**
     * The console command description.
     */
    protected $description = 'Import states, cities and pincodes from a CSV file (state,city,pincode,area)';

    public function handle()
    {
        $file = $this->option('file') ?: storage_path('imports/india_pincodes.csv');

        if (!file_exists($file)) {
            $this->error("CSV file not found: {$file}");
            return 1;
        }

        $handle = fopen($file, 'r');
        if ($handle === false) {
            $this->error('Failed to open file');
            return 1;
        }

        $this->info('Starting import from ' . $file);

        $header = fgetcsv($handle);
        $columns = array_map('strtolower', $header ?: []);

        // prepare column index lookups to handle variable CSV formats
        $findIndex = function(array $patterns) use ($columns) {
            foreach ($columns as $i => $col) {
                foreach ($patterns as $pat) {
                    if (strpos($col, $pat) !== false) {
                        return $i;
                    }
                }
            }
            return null;
        };

        $stateIdx = $findIndex(['state', 'statename', 'state_name']);
        $cityIdx = $findIndex(['city', 'district', 'office', 'officename', 'division', 'region']);
        $pincodeIdx = $findIndex(['pincode', 'pin', 'postal']);
        $areaIdx = $findIndex(['officename', 'office_name', 'area']);

        $rowCount = 0;
        while (($row = fgetcsv($handle)) !== false) {
            $rowCount++;

            $stateName = $stateIdx !== null ? trim($row[$stateIdx] ?? '') : '';
            $cityName = $cityIdx !== null ? trim($row[$cityIdx] ?? '') : '';
            $pincode = $pincodeIdx !== null ? trim($row[$pincodeIdx] ?? '') : '';
            $area = $areaIdx !== null ? trim($row[$areaIdx] ?? '') : '';

            // normalize and validate pincode
            $pincode = preg_replace('/\D/', '', $pincode);
            if (!$pincode || strlen($pincode) !== 6) {
                $this->warn("Skipping invalid row #{$rowCount}");
                continue;
            }

            $stateName = $stateName ? Str::title(Str::lower($stateName)) : '';
            $cityName = $cityName ? Str::title(Str::lower($cityName)) : '';

            $state = null;
            $city = null;
            if ($stateName && $cityName) {
                $state = State::firstOrCreate(['name' => $stateName]);
                $city = City::firstOrCreate(['state_id' => $state->id, 'name' => $cityName]);
            }

            // Ensure one record per pincode value. Attach city when available.
            $pincodeRecord = Pincode::firstOrCreate(['pincode' => $pincode], ['area' => $area]);
            if ($city && !$pincodeRecord->city_id) {
                $pincodeRecord->city_id = $city->id;
                $pincodeRecord->save();
            }

            if ($rowCount % 500 === 0) {
                $this->info("Imported {$rowCount} rows...");
            }
        }

        fclose($handle);

        $this->info("Import completed. Rows processed: {$rowCount}");
        return 0;
    }
}
