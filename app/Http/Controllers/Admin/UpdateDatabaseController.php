<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Carbon\Carbon;
use App\Models\AllUsers;
use App\Models\ApiStat;

class UpdateDatabaseController extends Controller
{
    // get the meta data of the api
    public function getApiStats($api="", $tbname = "")
    {
        try {

            $client = new Client();
            $api_stat = new ApiStat();
            $url = env('API_URL') . $api;
            $accessToken = env('API_ACCESS_TOKEN');
            $response = false;
            $startDate = Carbon::now()->format('Y-m-d\TH:i:s.u\Z');
            $endDate = Carbon::now()->format('Y-m-d\TH:i:s\Z');
            $response = $client->request('GET', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . env('API_ACCESS_TOKEN'),
                ],
                'query' => [
                    'min_updated_datetime' => $startDate,
                    'max_updated_datetime' => $endDate,
                    // 'status' =>'Payment Failure',
                    'page' => 1,
                    'page_size' => 10,
                ],
            ]);

            $statuscode = $response->getStatusCode();

            if ($statuscode == 200) {
                $body = $response->getBody()->getContents();
                $response = json_decode($body, true);
            }

            // Store the stats
            $api_stat->started_at = $startDate;
            $api_stat->api_source = $api;
            $api_stat->tbname = $tbname;
            $api_stat->has_request_completed = 0;
            $api_stat->has_error = 0;
            $api_stat->tbname = $tbname;
            $api_stat->ended_at = $endDate;
            $api_stat->url = $url;

            $api_stat->save();
           
        } catch (\Exception $e) {
            saveLog($e->getMessage());
        }
    }

    // save the data which we get from the api
    public function saveUsersdata()
    {
        $api = "users";
        ini_set('memory_limit', '-1');
        set_time_limit(0);


        $this->getApiStats($api);

        dd(" doe ");


        try {
            $client = new Client();
            $url = env('API_URL') . $api;
            $accessToken = env('API_ACCESS_TOKEN');

            $startDate = Carbon::parse('2023-01-10T19:34:27.892591Z')->format('Y-m-d\TH:i:s.u\Z');

            $endDate = Carbon::now()->format('Y-m-d\TH:i:s\Z');

            // $date = Carbon::create(2024, 7, 29, 7, 0, 0, 'UTC')->format('Y-m-d\TH:i:s\Z');

            $response = $client->request('GET', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . env('API_ACCESS_TOKEN'),
                ],
                'query' => [
                    'min_updated_datetime' => $startDate,
                    'max_updated_datetime' => $endDate,
                    // 'status' =>'Payment Failure',
                    'page' => 1,
                    'page_size' => 10,
                ],
            ]);

            $statuscode = $response->getStatusCode();

            if ($statuscode == 200) {
                $body = $response->getBody()->getContents();
            }

            $body = json_decode($body, true);

            $users = $body["data"];
            foreach ($users as $user) {
                $attributes = $user['attributes'];
                $user_id = $user['id'];
                $user_exists = AllUsers::where('user_id', $user_id)->first();
                if ($user_exists) {
                    continue;
                }
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
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function saveDataUpdateStats($table_name, $last_update, $has_error)
    {
    }
}
