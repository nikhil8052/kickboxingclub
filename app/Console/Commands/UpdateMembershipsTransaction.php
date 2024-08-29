<?php

namespace App\Console\Commands;

use App\Models\MembershipTransaction;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Exception;

class UpdateMembershipsTransaction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-memberships-transaction';

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
        //transaction_datetime
        $latestDate = MembershipTransaction::max('transaction_datetime');

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
        $url = env('API_URL') . 'membership_transactions';
        $accessToken = env('API_ACCESS_TOKEN');

        $currentPage = 1;
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

                    $savedTransactions = $this->saveMembershipsTransaction($data['data']);

                    if($savedTransactions) {
                        $totalPages = $data['meta']['pagination']['pages'] ?? 0;
                        if ($currentPage >= $totalPages) {
                            $hasMorePages = false;
                        } else {
                            $currentPage++;
                            sleep(15);
                        }
                    } else {
                        $this->error('Failed to save Meembership transaction data.');
                        break;
                    }
                } else {
                    $this->error('API request failed with status code ' . $response->getStatusCode());
                    $hasMorePages = false;
                }
            } catch (Exception $e) {
                // $this->error('Error: ' . $e->getMessage());
                // savelog("Error:","SavedMembershipsTransactions", $e->getMessage());
                $hasMorePages = false;
            }
        }
    }

    public function saveMembershipsTransaction($membership_transaction){
        try {
            foreach($membership_transaction as $membership){
                $olddata = MembershipTransaction::where('membership_transactions_id',$membership['id'])->first();
                if(!$olddata){
                    $attributes = $membership['attributes'] ?? [];
                    $relationships = $membership['relationships'] ?? [];

                    $membership_transaction = new MembershipTransaction;
                    $membership_transaction->type = $membership['type'];
                    $membership_transaction->membership_name = $membership['attributes']['membership_name'];
                    $membership_transaction->membership_transactions_id = $membership['id'];
                    $membership_transaction->transaction_amount = $membership['attributes']['transaction_amount'];
                    $membership_transaction->user_id = $membership['relationships']['user']['data']['id'];
                    $membership_transaction->membership_instances_id = $membership['relationships']['membership_instance']['data']['id'];
                    $membership_transaction->transaction_datetime = Carbon::parse($membership['attributes']['transaction_datetime']);
                    $membership_transaction->transaction_date = Carbon::parse($membership['attributes']['transaction_datetime'])->format('Y-m-d');
                    $membership_transaction->save();
                }
            }
            return true;
        } catch(Exception $e) {
            // $this->error('Error saving transaction data: ' . $e->getMessage());
            // savelog("Error saving transaction data:","UpdateMembershipTransaction", $e->getMessage());
            return response()->json([$e->getMessage()]);
        }
       
    }
}
