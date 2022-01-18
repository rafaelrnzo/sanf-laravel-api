<?php


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Sanf\Core\Modules\Location\AdministrativeAreaModel;
use Sanf\Core\Modules\Location\LocationModel;

class LocationSeeder extends Seeder
{
    const FILES = [
        'm_administrative_area.json' => AdministrativeAreaModel::class,
        'm_location.json' => LocationModel::class,
    ];
    protected $chunk = 1000;

    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();
        try {
            $header = ['TABLE', 'JSON', 'EXECUTION TIME'];
            $body = [];
            $executionSeedStartTime = microtime(true);

            /* @var Model $model */
            foreach (self::FILES as $file => $model) {
                $this->command->getOutput()->writeln("<comment>Seeding:</comment> {$file}");
                $datas = json_decode(File::get("database/data/{$file}"), true);

                $startTime = microtime(true);
                $datas = collect($datas);
                $chunkedData = $datas->chunk($this->chunk);
                $this->command->getOutput()->progressStart(count($chunkedData));
                foreach ($chunkedData as $data) {
                    $model::query()->insertOrIgnore($data->toArray());
                    $this->command->getOutput()->progressAdvance();
                }

                $this->command->getOutput()->progressFinish();
                $runTime = round(microtime(true) - $startTime, 2);
                $body[] = [(new $model)->getTable(), $file, "{$runTime} seconds"];
            }

            $executionSeedEndTime = microtime(true);

            $seedTime = round($executionSeedEndTime - $executionSeedStartTime, 2);
            $body[] = ['ALL TABLE', '-', "{$seedTime} seconds"];
            $this->command->table($header, $body);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            dump($e);
            die;
        }

    }
}
