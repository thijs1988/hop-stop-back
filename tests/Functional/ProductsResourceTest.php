<?php


namespace App\Tests\Functional;

use App\Entity\User;
use App\Factory\BrandsFactory;
use App\Factory\CategoriesFactory;
use App\Factory\MediaFactory;
use App\Factory\ProductsFactory;
use App\Factory\UserFactory;
use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class ProductsResourceTest extends CustomApiTestCase
{
    public function testCreateProducts()
    {
        $client = self::createClient();
        $user = UserFactory::new()->create();
        $this->logInFactory($client, $user);
        $brand = BrandsFactory::new()->create();
        $category = CategoriesFactory::new()->create();
        $category1 = CategoriesFactory::new()->create();
        $category2 = CategoriesFactory::new()->create();
        $media = MediaFactory::new()->create();
        $media2 = MediaFactory::new()->create();
        $media3 = MediaFactory::new()->create();

        $data = [
            "name" => "string",
            "type" =>"string",
            "price" => 0,
            "brands" => '/api/brands/'.$brand->object()->getId(),
            "categories" => [
                '/api/categories/'.$category->object()->getId(),
                '/api/categories/'.$category1->object()->getId(),
                '/api/categories/'.$category2->object()->getId()
                ],
            "images" => [
                '/api/media/'.$media->object()->getId(),
                '/api/media/'.$media2->object()->getId(),
                '/api/media/'.$media3->object()->getId()
                ],
            "description"=> "string",
        ];

        $client->request('POST', '/api/products', [
            'json' => $data
        ]);
        $this->assertResponseStatusCodeSame(403);

        $user->refresh();
        $user->setRoles(['ROLE_ADMIN']);
        $user->save();
//        dd($user);
        $this->logInFactory($client, $user);

        $client->request('POST', '/api/products', [
            'json' => $data
        ]);
        $this->assertResponseStatusCodeSame(201);
    }

    public function testUpdateProducts()
    {
        $client = self::createClient();
        $user = UserFactory::new()->create();
        $product = ProductsFactory::new()->create();

        $this->logInFactory($client, $user);
        $client->request('PUT', '/api/products/'.$product->getId(), [
            'json' => [
                'price' => 1005,
            ]
        ]);
        $this->assertResponseStatusCodeSame(403);

        $user->refresh();
        $user->setRoles(['ROLE_ADMIN']);
        $user->save();

        $this->logInFactory($client, $user);

        $client->request('PUT', '/api/products/'.$product->getId(), [
            'json' => [
                'price' => 1005,
                'type' => 'DDH DIPA'
            ]
        ]);
        $this->assertResponseIsSuccessful();

        $client->request('GET', '/api/products/'.$product->getId());
        $this->assertJsonContains([
            'price' => 1005,
            'type' => 'DDH DIPA'
        ]);

        $client->request('PATCH', '/api/products/'.$product->getId(), [
            'headers' => ["Content-Type" => "application/merge-patch+json"],
            'json' => [
                'price' => 10105
            ]
        ]);
        $this->assertResponseIsSuccessful();

        $client->request('GET', '/api/products/'.$product->getId());
        $this->assertJsonContains([
            'price' => 10105
        ]);
    }

    public function testGetProducts()
    {
        $client = self::createClient();
        $user1 = UserFactory::new()->create();




        $product = ProductsFactory::new()->create();
        ProductsFactory::new()->create();
        ProductsFactory::new()->create();
        $client->request('GET', '/api/products');
//        $data = $client->getResponse()->toArray();
//        dd($data);
        $this->assertJsonContains(['hydra:totalItems' => 3]);

        $client->request('GET', '/api/products/'.$product->getId());
        $this->assertResponseStatusCodeSame(200);

        $this->logInFactory($client, $user1);
        $client->request('GET', '/api/products');
//        $data = $client->getResponse()->toArray();
//        dd($data);
        $this->assertJsonContains(['hydra:totalItems' => 3]);

        $client->request('GET', '/api/products/'.$product->getId());
        $this->assertResponseStatusCodeSame(200);
    }
}