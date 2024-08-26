<?php

namespace App\Console\Commands;

use App\Models\BillingCycle;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Exception;

class UpdateBillingCycle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-billing-cycle';

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
        $latestDate = BillingCycle::max('start_datetime');

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
        $url = env('API_URL') . 'membership_instances';
        $accessToken = env('API_ACCESS_TOKEN');

        $currentPage = 2;
        $pageSize = 500;
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

                    $savedBilling = $this->saveBillingCycle($data['data']);

                    if($savedBilling) {
                        $totalPages = $data['meta']['pagination']['pages'] ?? 0;
                        if ($currentPage >= $totalPages) {
                            $hasMorePages = false;
                        } else {
                            $currentPage++;
                            sleep(15);
                        }
                    } else {
                        $this->error('Failed to save billing data.');
                        break;
                    }
                } else {
                    $this->error('API request failed with status code ' . $response->getStatusCode());
                    $hasMorePages = false;
                }
            } catch (Exception $e) {
                // $this->error('Error: ' . $e->getMessage());
                savelog("Error:","UpdateBillingCycle", $e->getMessage());
                $hasMorePages = false;
            }
        }
    }

    public function saveBillingCycle($billings){
        try{    
            foreach($billings as $billing){
                $olddata = BillingCycle::where('billing_id',$billing['id'])->first();
                if(!$olddata){
                    $membership_instance_id = $billing['id'];
                    $renewal_rate = $billing['attributes']['renewal_rate'];
                    $billing_cycles = $billing['attributes']['billing_cycles'];
                    $location_id = $billing['relationships']['purchase_location']['data']['id'] ?? null;

                    foreach($billing_cycles as $bill){
                        $billingCycle = new BillingCycle;
                        $billingCycle->billing_id = $bill['id'];
                        $billingCycle->membership_instance_id = $membership_instance_id;
                        $billingCycle->start_datetime = $bill['start_datetime'] == null 
                            ? null 
                            : Carbon::parse($bill['start_datetime']); 

                        $startDatetime = $billingCycle->start_datetime == null 
                            ? null 
                            : $billingCycle->start_datetime->setTimezone('America/Los_Angeles'); 

                        $billingCycle->start_date_copy = $startDatetime == null 
                            ? null 
                            : $startDatetime->format('Y-m-d');

                        $billingCycle->end_datetime = $bill['end_datetime'] == null 
                            ? null 
                            : Carbon::parse($bill['end_datetime']); 

                        $endDatetime = $billingCycle->end_datetime == null 
                            ? null 
                            : $billingCycle->end_datetime->setTimezone('America/Los_Angeles'); 

                        $billingCycle->end_date_copy = $endDatetime == null 
                            ? null 
                            : $endDatetime->format('Y-m-d');
                    
                        $billingCycle->renewal_rate = $renewal_rate;
                        $billingCycle->location_id = $location_id;
                        $billingCycle->save();
                    }
                }
            }
            return true;
        }catch(Exception $e){
            $this->error('Error saving billing data: ' . $e->getMessage());
            // savelog("Error saving billing data:","UpdateBillingCycle", $e->getMessage());
            return false;
        }
    }
}
