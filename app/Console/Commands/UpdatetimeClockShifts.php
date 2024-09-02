<?php

namespace App\Console\Commands;

use App\Models\TimeClockShift;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Exception;

class UpdatetimeClockShifts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:updatetime-clock-shifts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $latestDate = TimeClockShift::max('end_datetime');

        if (!$latestDate) {
            // $this->info('No orders found.');
            // return;
            $latestDate = null;
        }

        if($latestDate != null){
            $startDate = Carbon::parse($latestDate)->format('Y-m-d\TH:i:s\Z');
        } else {
            $startDate = null;
        }
        // $startDate = null;
        $this->info("Latest date placed : $latestDate");

        $client = new Client();
        $url = env('API_URL') . 'time_clock_shifts';
        $accessToken = env('API_ACCESS_TOKEN');

        $currentPage = 1;
        $pageSize = 100;
        $hasMorePages = true;

        while ($hasMorePages) {
            try {
                $response = $client->request('GET', $url, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $accessToken,
                    ],
                    'query' => [
                        'min_updated_datetime' => $startDate,
                        'page' => $currentPage,
                        'page_size' => $pageSize,
                    ],
                ]);

                if ($response->getStatusCode() == 200) {
                    $body = $response->getBody()->getContents();
                    $data = json_decode($body, true);

                    $savedUsers = $this->saveShiftData($data['data']);

                    if ($savedUsers) {
                        $totalPages = $data['meta']['pagination']['pages'] ?? 0;
                        if ($currentPage >= $totalPages) {
                            $hasMorePages = false;
                        } else {
                            $currentPage++;
                            sleep(30);
                        }
                    } else {
                        $this->error('Failed to save Shifts data.');
                        break;
                    }
                } else {
                    $this->error('API request failed with status code ' . $response->getStatusCode());
                    $hasMorePages = false;
                }
            } catch (Exception $e) {
                // $this->error('Error: ' . $e->getMessage());
                savelog("Error:","UpdateShifts", $e->getMessage());
                $hasMorePages = false;
            }
        }
    }

    public function saveShiftData($shifts)
    {
        try {

            foreach($shifts as $data){  
                $oldRecord = TimeClockShift::where('time_clock_id',$data['id'])->first();
                if(!$oldRecord){           
                    $attributes = $data['attributes'] ?? [];
                    $relationships = $data['relationships'] ?? [];
                
                    $time_clock = new TimeClockShift();
                    $time_clock->type = $data['type'];
                    $time_clock->time_clock_id = $data['id'];

                    $enddate = $attributes['end_datetime'] ? Carbon::parse($attributes['end_datetime']) : null;
                    $startdate = $attributes['start_datetime'] ? Carbon::parse($attributes['start_datetime']) : null;

                    $time_clock->start_datetime =  $startdate;
                    $time_clock->end_datetime =  $enddate;

                    $time_clock->start_datetime_copy =  convertToUSATimezone($startdate);
                    $time_clock->end_datetime_copy =  convertToUSATimezone($enddate);

                    $time_clock->duration = $attributes['duration'];
                    $time_clock->user_has_turf_access = $attributes['user_has_turf_access'];
                    $time_clock->relationships = json_encode($relationships);
                    $time_clock->employee_id = $relationships['employee']['data']['id'];
                    $time_clock->location_id = $relationships['location']['data']['id'];

                    
                    if($enddate){
                        $time_clock->save(); 
                    }
                }

            }
            return true;
        } catch(Exception $e) {
             $this->error('Error saving shifts data: ' . $e->getMessage());
             savelog("Error saving shifts data:","UpdateShifts", $e->getMessage());
             return false;
        }
       
    }
}
