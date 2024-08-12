<?php

namespace App\Console\Commands;

use App\Models\AllUsers;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Exception;

class UpdateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update user data from external API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $latestDate = AllUsers::max('date_joined');

        if (!$latestDate) {
            $this->info('No users found.');
            return;
        }

        $startDate = Carbon::parse($latestDate)->format('Y-m-d\TH:i:s\Z');

        $this->info("Latest date joined: $latestDate");

        $client = new Client();
        $url = env('API_URL') . 'users';
        $accessToken = env('API_ACCESS_TOKEN');

        $currentPage = 1;
        $pageSize = 50;
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

                    $savedUsers = $this->saveUsersdata($data['data']);

                    if ($savedUsers) {
                        $totalPages = $data['meta']['pagination']['pages'] ?? 0;
                        if ($currentPage >= $totalPages) {
                            $hasMorePages = false;
                        } else {
                            $currentPage++;
                        }
                    } else {
                        $this->error('Failed to save users data.');
                        break;
                    }
                } else {
                    $this->error('API request failed with status code ' . $response->getStatusCode());
                    $hasMorePages = false;
                }
            } catch (Exception $e) {
                // $this->error('Error: ' . $e->getMessage());
                savelog("Error:","UpdateUser", $e->getMessage());
                $hasMorePages = false;
            }
        }
    }

    public function saveUsersdata($users)
    {
        try {
            foreach ($users as $user) {
                $oldUser = AllUsers::where('user_id',$user['id'])->first();
                if(!$oldUser){
                    $attributes = $user['attributes'];
                
                    $or = new AllUsers();
                    $or->type = $user['type'];
                    $or->user_id = $user['id'];
                    $or->email = $attributes['email'] ?? null;
                    $or->first_name = $attributes['first_name'] ?? null;
                    $or->last_name = $attributes['last_name'] ?? null;
                    $or->birth_date = $attributes['birth_date'] ?? null;
                    $or->phone_number = $attributes['phone_number'] ?? null;
                    $or->address_line1 = $attributes['address_line1'] ?? null;
                    $or->address_line2 = $attributes['address_line2'] ?? null;
                    $or->address_line3 = $attributes['address_line3'] ?? null;
                    $or->city = $attributes['city'] ?? null;
                    $or->state_province = $attributes['state_province'] ?? null;
                    $or->postal_code = $attributes['postal_code'] ?? null;
                    $or->address_sorting_code = $attributes['address_sorting_code'] ?? null;
                    $or->country = $attributes['country'] ?? null;
                    $or->full_name = $attributes['full_name'] ?? null;
                    $or->gender = $attributes['gender'] ?? null;
                    $or->emergency_contact_name = $attributes['emergency_contact_name'] ?? null;
                    $or->emergency_contact_relationship = $attributes['emergency_contact_relationship'] ?? null;
                    $or->emergency_contact_phone = $attributes['emergency_contact_phone'] ?? null;
                    $or->emergency_contact_email = $attributes['emergency_contact_email'] ?? null;
                    $or->signed_waiver = $attributes['signed_waiver'] ?? null;
                    $or->waiver_signed_datetime = $attributes['waiver_signed_datetime'] ?? null;
                    $or->date_joined = $attributes['date_joined'] ?? null;
                    $or->marketing_opt_in = $attributes['marketing_opt_in'] ?? null;
                    $or->is_opted_in_to_sms = $attributes['is_opted_in_to_sms'] ?? null;
                    $or->has_vip_tag_cache = $attributes['has_vip_tag_cache'] ?? null;
                    $or->apply_account_balance_to_fees = $attributes['apply_account_balance_to_fees'] ?? null;
                    $or->is_minimal = $attributes['is_minimal'] ?? null;
                    $or->permissions = json_encode($attributes['permissions'] ?? []);
                    $or->account_balance = $attributes['account_balance'] ?? null;
                    $or->account_balances = json_encode($attributes['account_balances'] ?? []);
                    $or->third_party_sync = $attributes['third_party_sync'] ?? null;
                    $or->completed_class_count = $attributes['completed_class_count'] ?? null;
                    $or->company_name = $attributes['company_name'] ?? null;
                    $or->archived_at = $attributes['archived_at'] ?? null;
                    $or->is_external_user = $attributes['is_external_user'] ?? null;
                    $or->merged_into_id = $attributes['merged_into_id'] ?? null;
                    $or->waivers = json_encode($attributes['waivers'] ?? []);
                    $or->has_unsigned_waivers = $attributes['has_unsigned_waivers'] ?? null;
                    $or->marketing_logs = json_encode($attributes['marketing_logs'] ?? []);
                    $or->formatted_address = json_encode($attributes['formatted_address'] ?? []);
                    $or->required_legal_documents = json_encode($attributes['required_legal_documents'] ?? []);
                    $or->pronouns = $attributes['pronouns'] ?? null;
                    $or->search_priority_category = $attributes['search_priority_category'] ?? null;
                    $or->relationships = json_encode($user['relationships'] ?? []);
                    $or->last_region = json_encode($user['relationships']['last_region']['data'] ?? []);
                    $or->home_location_type = $user['relationships']['home_location']['data']['type'] ?? null;
                    $or->home_location_id = $user['relationships']['home_location']['data']['id'] ?? null;
                    $or->tags = json_encode($user['relationships']['tags']['data'] ?? []);
                    $or->profile_image = json_encode($user['relationships']['profile_image']['data'] ?? []);

                    $or->save();
                }
                
            }
            return true;
        } catch (Exception $e) {
            // $this->error('Error saving user data: ' . $e->getMessage());
            savelog("Error saving user data:","UpdateUser", $e->getMessage());
            return false;
        }
    }
}
