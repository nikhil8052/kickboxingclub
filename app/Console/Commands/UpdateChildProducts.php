<?php

namespace App\Console\Commands;

use App\Models\ChildProduct;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Exception;

class UpdateChildProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-child-products';

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
        $latestDate = ChildProduct::max('created_at');

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

                    $savedUsers = $this->saveChildProductsData($data['data']);

                    if ($savedUsers) {
                        $totalPages = $data['meta']['pagination']['pages'] ?? 0;
                        if ($currentPage >= $totalPages) {
                            $hasMorePages = false;
                        } else {
                            $currentPage++;
                            sleep(30);
                        }
                    } else {
                        $this->error('Failed to save child products data.');
                        break;
                    }
                } else {
                    $this->error('API request failed with status code ' . $response->getStatusCode());
                    $hasMorePages = false;
                }
            } catch (Exception $e) {
                // $this->error('Error: ' . $e->getMessage());
                savelog("Error:","UpdateChildProducts", $e->getMessage());
                $hasMorePages = false;
            }
        }
    }

    public function saveChildProductsData($products)
    {
        try {
            foreach($products as $productData){
                $olddata = ChildProduct::where('child_product_id',$productData['id'])->first();
                if(!$olddata){
                    $product = new ChildProduct();
                    $product->child_product_id = $productData['id'];
                    $product->title = $productData['attributes']['title'];
                    $product->description = $productData['attributes']['description'];
                    $product->is_discountable = $productData['attributes']['is_discountable'];
                    $product->slug = $productData['attributes']['slug'];
                    $product->date_created = $productData['attributes']['date_created'] ? Carbon::parse($productData['attributes']['date_created']) : null;
                    $product->date_updated = $productData['attributes']['date_updated'] ? Carbon::parse($productData['attributes']['date_updated']) : null;
                    $product->supported_currencies = json_encode($productData['attributes']['supported_currencies']);
                    $product->options = json_encode($productData['attributes']['options']);
                    $product->is_public = $productData['attributes']['is_public'];
                    $product->default_inventoriable = $productData['attributes']['default_inventoriable'];
                    $product->is_active = $productData['attributes']['is_active'];
                    $product->user_has_any_locations = $productData['attributes']['user_has_any_locations'];
                    $product->user_has_all_locations = $productData['attributes']['user_has_all_locations'];
                    $product->is_live_stream = $productData['attributes']['is_live_stream'];
                    $product->is_first_timer_only = $productData['attributes']['is_first_timer_only'];
                    $product->is_intro_offer = $productData['attributes']['is_intro_offer'];
                    $product->price = $productData['attributes']['price'];
                    $product->pricing = json_encode($productData['attributes']['pricing']);
                    $product->upc = $productData['attributes']['upc'];
                    $product->sub_title = $productData['attributes']['sub_title'];
                    $product->sku = $productData['attributes']['sku'];
                    $product->relationships = json_encode($productData['relationships']);
                    $product->product_class_id = $productData['relationships']['product_class']['data']['id'];
                    $product->product_class_type = $productData['relationships']['product_class']['data']['type'];
                    $product->parent_type = $productData['relationships']['product_class']['data']['type'];
                    $product->parent_id = $productData['relationships']['parent']['data']['id'];
        
                    $product->save();
                }
            }
            return true;
        } catch(Exception $e) {
            dd($e);
            return false;
        }
       
    }
}
