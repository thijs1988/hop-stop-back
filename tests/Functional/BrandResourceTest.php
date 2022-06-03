<?php


namespace App\Tests\Functional;


use App\Factory\BrandsFactory;
use App\Factory\UserFactory;
use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class BrandResourceTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    public function testCreateBrand()
    {
        $client = self::createClient();
        $user = UserFactory::new()->create();

        $this->logInFactory($client, $user);
        $client->request('POST', '/api/brands', [
            'json' => [
                'name' => 'Cloudwater'
            ]
        ]);
        $this->assertResponseStatusCodeSame(403);

        $user->refresh();
        $user->setRoles(['ROLE_ADMIN']);
        $user->save();

        $this->logInFactory($client, $user);
        $client->request('POST', '/api/brands', [
            'json' => ['name' => 'Cloudwater']
        ]);
        $this->assertResponseStatusCodeSame(201);
    }

    public function testUpdateBrand()
    {
        $client = self::createClient();
        $user = UserFactory::new()->create();
        $brand = BrandsFactory::new()->create();

//        dd($user, $brand);
        $this->logInFactory($client, $user);
        $client->request('PUT', '/api/brands/'.$brand->getId(), [
            'json' => [
                'name' => 'apex'
            ]
        ]);
        $this->assertResponseStatusCodeSame(403);

        $user->refresh();
        $user->setRoles(['ROLE_ADMIN']);
        $user->save();

        $this->logInFactory($client, $user);

        $client->request('PUT', '/api/brands/'.$brand->getId(), [
            'json' => [
                'name' => 'DDH DIPA'
            ]
        ]);
        $this->assertResponseIsSuccessful();

        $client->request('GET', '/api/brands/'.$brand->getId());
        $this->assertJsonContains([
            'name' => 'DDH DIPA'
        ]);

        $client->request('PATCH', '/api/brands/'.$brand->getId(), [
            'headers' => ["Content-Type" => "application/merge-patch+json"],
            'json' => [
                'name' => 'jwz'
            ]
        ]);
        $this->assertResponseIsSuccessful();

        $client->request('GET', '/api/brands/'.$brand->getId());
        $this->assertJsonContains([
            'name' => 'jwz'
        ]);
    }

    public function testGetBrands()
    {
        $client = self::createClient();
        $user1 = UserFactory::new()->create();

        $product = BrandsFactory::new()->create();
        BrandsFactory::new()->create();
        BrandsFactory::new()->create();

        $client->request('GET', '/api/brands');

        $this->assertJsonContains(['hydra:totalItems' => 3]);

        $client->request('GET', '/api/brands/'.$product->getId());
        $this->assertResponseStatusCodeSame(200);

        $this->logInFactory($client, $user1);
        $client->request('GET', '/api/brands');

        $this->assertJsonContains(['hydra:totalItems' => 3]);

        $client->request('GET', '/api/brands/'.$product->getId());
        $this->assertResponseStatusCodeSame(200);
    }

}