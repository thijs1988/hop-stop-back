<?php


namespace App\Tests\Functional;


use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class TransactionResourceTest extends CustomApiTestCase
{
    public function testCreateTransaction(){
        $client = self::createClient();
        $client->request('POST', '/pay', [
              'json' => [
                  "amount" => "100,00",
                  ]
        ]);
    }

}