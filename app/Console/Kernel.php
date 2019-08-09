<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use LireinCore\YMLParser\YML;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $urls = [
                "http://www.trenazhery.ru/market2.xml", 
                "http://www.radio-liga.ru/yml.php", 
                "http://armprodukt.ru/bitrix/catalog_export/yandex.php"
            ];
            $yml = new YML();
            try {
                foreach ($urls as $url) {
                $yml->parse($url);
                    foreach ($yml->getOffers() as $offer) {
                        $offerData = $offer->getData();
                        $sha = sha1(
                            ($offerData['url'] ?? 'default') 
                            . ($offerData['price'] ?? 'default') 
                            . ($offerData['name'] ?? $offerData['model'])
                            . ($offerData['pictures'][0] ?? 'default')
                            . ($offerData['description'] ?? 'default')   
                        );

                        $dataFromDb = DB::table('items')
                                        ->select('sha', 'name', 'id')
                                        ->where('name', '=', ($offerData['name'] ?? $offerData['model']))
                                        ->first();
                    
                        if ($dataFromDb !== null && $dataFromDb !== $sha) {
                            DB::table('items')
                                ->where('id', $dataFromDb->id)
                                ->update(
                                    ['url' => ($offerData['url'] ?? 'default'), 
                                    'price' => ($offerData['price'] && 'default'),
                                    'name' => ($offerData['name'] ?? $offerData['model']),
                                    'picture' => ($offerData['pictures'][0] ?? 'default'),
                                    'description' => ($offerData['description'] ?? 'default'),
                                    'sha' => $sha
                                    ]
                                );
                        } elseif($dataFromDb === null) {
                            DB::table('items')->insert(
                                ['url' => ($offerData['url'] ?? 'default'), 
                                'price' => ($offerData['price'] && 'default'),
                                'name' => ($offerData['name'] ?? $offerData['model']),
                                'picture' => ($offerData['pictures'][0] ?? 'default'),
                                'description' => ($offerData['description'] ?? 'default'),
                                'sha' => $sha
                                ]
                            );
                        }
                    }
                }
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        
        })->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
