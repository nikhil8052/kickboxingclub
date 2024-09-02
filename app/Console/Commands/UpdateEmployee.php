<?php

namespace App\Console\Commands;

use App\Models\Employees;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Exception;

class UpdateEmployee extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-employee';

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
        $latestDate = Employees::max('created_at');

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
        $url = env('API_URL') . 'employees';
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
                        // 'min_updated_datetime' => $startDate,
                        'page' => $currentPage,
                        'page_size' => $pageSize,
                    ],
                ]);

                if ($response->getStatusCode() == 200) {
                    $body = $response->getBody()->getContents();
                    $data = json_decode($body, true);

                    $savedUsers = $this->saveEmployeesdata($data['data']);

                    if ($savedUsers) {
                        $totalPages = $data['meta']['pagination']['pages'] ?? 0;
                        if ($currentPage >= $totalPages) {
                            $hasMorePages = false;
                        } else {
                            $currentPage++;
                            sleep(30);
                        }
                    } else {
                        $this->error('Failed to save employee data.');
                        break;
                    }
                } else {
                    $this->error('API request failed with status code ' . $response->getStatusCode());
                    $hasMorePages = false;
                }
            } catch (Exception $e) {

                savelog("Error:","UpdateShifts", $e->getMessage());
                $hasMorePages = false;
            }
        }
    }

    public function saveEmployeesdata($employee)
    {
        try {

            foreach($employee as $data){  
                $oldRecord = Employees::where('employee_id',$data['id'])->first();
                if(!$oldRecord){           
                    $attributes = $data['attributes'];
            
                    $or = new Employees();
                    $or->type = $data['type'];
                    $or->employee_id = $data['id'];
                    $or->payroll_id = $attributes['payroll_id'] ?? null;
                    $or->is_active = $attributes['is_active'] ?? null;
                    $or->can_chat = $attributes['can_chat'] ?? null;
                    $or->is_beta_user = $attributes['is_beta_user'] ?? null;
                    $or->relationships = json_encode($data['relationships'] ?? []);
                    $or->user_type = $data['relationships']['user']['data']['type'] ?? null;
                    $or->user_id = $data['relationships']['user']['data']['id'] ?? null;
                    $or->recent_location_type = $data['relationships']['recent_location']['data']['type'] ?? null;
                    $or->recent_location_id = $data['relationships']['recent_location']['data']['id'] ?? null;
                    $or->public_profile_type = $data['relationships']['public_profile']['data']['type'] ?? null;
                    $or->public_profile_id = $data['relationships']['public_profile']['data']['id'] ?? null;
                    $or->groups = json_encode($data['relationships']['groups']['data'] ?? []);
                    $or->turfs = json_encode($data['relationships']['turfs']['data'] ?? []);

                    $or->save();
                }

            }
            return true;
        } catch(Exception $e) {
             $this->error('Error saving employee data: ' . $e->getMessage());
             savelog("Error saving employee data:","UpdateEmployee", $e->getMessage());
             return false;
        }
       
    }
}
