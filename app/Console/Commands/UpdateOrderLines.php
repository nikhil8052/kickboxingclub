<?php

namespace App\Console\Commands;

use App\Models\OrderLine;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Exception;


class UpdateOrderLines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-order-lines';

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
        $latestDate = OrderLine::max('created_at');

        if (!$latestDate) {
            $latestDate = null;
        }
        if($latestDate != null) {
            $startDate = Carbon::parse($latestDate)->format('Y-m-d\TH:i:s\Z');
        } else {
            $startDate = null;
        }

        $this->info("Latest date placed : $latestDate");

        $client = new Client();
        $url = env('API_URL') . 'order_lines';
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
                        // 'min_updated_datetime' => $startDate,
                        'page' => $currentPage,
                        'page_size' => $pageSize,
                    ],
                ]);

                if ($response->getStatusCode() == 200) {
                    $body = $response->getBody()->getContents();
                    $data = json_decode($body, true);

                    $savedUsers = $this->saveOrderLines($data['data']);

                    if ($savedUsers) {
                        $totalPages = $data['meta']['pagination']['pages'] ?? 0;
                        if ($currentPage >= $totalPages) {
                            $hasMorePages = false;
                        } else {
                            $currentPage++;
                            sleep(30);
                        }
                    } else {
                        $this->error('Failed to save order line data.');
                        break;
                    }
                } else {
                    $this->error('API request failed with status code ' . $response->getStatusCode());
                    $hasMorePages = false;
                }
            } catch (Exception $e) {
                // $this->error('Error: ' . $e->getMessage());
                savelog("Error:","UpdateOrderLines", $e->getMessage());
                $hasMorePages = false;
            }
        }
    }

    public function saveOrderLines($orderlines)
    {
        try {
            foreach($orderlines as $lineData){

                $olddata = OrderLine::where('order_line_id',$lineData['id'])->first();
                if(!$olddata){
                    $line = new OrderLine();
                    $line->order_line_id = $lineData['id'];
                    $line->transaction_data = json_encode($lineData['attributes']['transaction_data']);
                    $line->transaction_type = $lineData['attributes']['transaction_data'][0]['transaction_type'] ?? null;
                    $line->transaction_id = $lineData['attributes']['transaction_data'][0]['transaction_id'] ?? null;
                    $line->membership_instance_id = $lineData['attributes']['transaction_data'][0]['membership_instance_id'] ?? null;
                    $line->is_payment_deferred = $lineData['attributes']['is_payment_deferred'];
                    $line->line_item_discount_amount = $lineData['attributes']['line_item_discount_amount'];
                    $line->line_item_discount_note = $lineData['attributes']['line_item_discount_note'];
                    $line->line_subtotal = $lineData['attributes']['line_subtotal'];
                    $line->line_subtotal_incl_tax = $lineData['attributes']['line_subtotal_incl_tax'];
                    $line->line_subtotal_incl_tax_excl_line_item_discount = $lineData['attributes']['line_subtotal_incl_tax_excl_line_item_discount'];
                    $line->line_subtotal_excl_line_item_discount = $lineData['attributes']['line_subtotal_excl_line_item_discount'];
                    $line->line_subtotal_pre_discount = $lineData['attributes']['line_subtotal_pre_discount'];
                    $line->line_taxes = json_encode($lineData['attributes']['line_taxes']);
                    $line->line_total = $lineData['attributes']['line_total'];
                    $line->options = json_encode($lineData['attributes']['options']);
                    $line->quantity = $lineData['attributes']['quantity'];
                    $line->should_display_price_include_tax = $lineData['attributes']['should_display_price_include_tax'];
                    $line->status = $lineData['attributes']['status'];
                    $line->sub_title = $lineData['attributes']['sub_title'];
                    $line->title = $lineData['attributes']['title'];
                    $line->unit_subtotal = $lineData['attributes']['unit_subtotal'];
                    $line->unit_total = $lineData['attributes']['unit_total'];
                    $line->variant_information = $lineData['attributes']['variant_information'];
                    $line->has_options = $lineData['attributes']['has_options'];
                    $line->child_orders = json_encode($lineData['relationships']['child_orders']['data']);
                    $line->order_id = $lineData['relationships']['order']['data']['id'];
                    $line->product_id = $lineData['relationships']['product']['data']['id'];
                    $line->order_type = $lineData['relationships']['order']['data']['type'];
                    $line->product_type = $lineData['relationships']['product']['data']['type'];
                    $line->relationships = json_encode($lineData['relationships']);
                    $line->save();
                }
            }
            return true;
        } catch(Exception $e) {
            dd($e);
            return false;
        }
       
    }
}
