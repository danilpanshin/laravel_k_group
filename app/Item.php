<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use LireinCore\YMLParser\YML;

class Item extends Model
{
    public static function getData($name)
    {
        return Item::select("name", "price", "description", "url", "picture")
            ->where("name", "=", "{$name}")
            ->first();
    }

    public static function getDataById($id)
    {
        return Item::select("name", "price", "description", "url", "picture")
            ->where("id", "=", "{$id}")
            ->first();
    }

    public static function startImport()
    {
        $urls = [
            "http://www.trenazhery.ru/market2.xml",
            "http://www.radio-liga.ru/yml.php",
            "http://armprodukt.ru/bitrix/catalog_export/yandex.php",
            'http://static.ozone.ru/multimedia/yml/facet/div_soft.xml'
        ];
        $yml = new YML();
        try {
            foreach ($urls as $url) {
                $yml->parse($url);
                foreach ($yml->getOffers() as $offer) {
                    if (!$offerData = $offer->getData()) {
                        continue;
                    }
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
                                [
                                    'url' => ($offerData['url'] ?? 'default'),
                                    'price' => ($offerData['price'] && 'default'),
                                    'name' => ($offerData['name'] ?? $offerData['model']),
                                    'picture' => ($offerData['pictures'][0] ?? 'default'),
                                    'description' => ($offerData['description'] ?? 'default'),
                                    'sha' => $sha
                                ]
                            );
                    } elseif ($dataFromDb === null) {
                        DB::table('items')->insert(
                            [
                                'url' => ($offerData['url'] ?? 'default'),
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
    }
}
