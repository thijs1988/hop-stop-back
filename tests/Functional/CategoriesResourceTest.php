<?php


namespace App\Tests\Functional;


use App\Factory\CategoriesFactory;
use App\Factory\UserFactory;
use App\Test\CustomApiTestCase;

class CategoriesResourceTest extends CustomApiTestCase
{
    public function testCreateCategory()
    {
        $client = self::createClient();
        $user = UserFactory::new()->create();
        $this->logInFactory($client, $user);

        $client->request('POST', '/api/categories', [
            'json' => [
                'name' => 'ipa',
            ]
        ]);
        $this->assertResponseStatusCodeSame(403);

        $user->refresh();
        $user->setRoles(['ROLE_ADMIN']);
        $user->save();

        $this->logInFactory($client, $user);
        $client->request('POST', '/api/categories', [
            'json' => [
                'name' => 'parent',
            ]
        ]);
        $this->assertResponseStatusCodeSame(201);

        $this->logInFactory($client, $user);
        $client->request('POST', '/api/categories', [
            'json' => [
                'name' => 'child',
                'parent' => '/api/categories/1'
            ]
        ]);
        $this->assertResponseStatusCodeSame(201);

        $client->request('POST', '/api/categories', [
            'json' => [
                'name' => 'child1',
                'parent' => '/api/categories/1',
            ]
        ]);
        $this->assertResponseStatusCodeSame(201);
        $data = $client->getResponse()->toArray();


        $client->request('POST', '/api/categories', [
            'json' => [
                'name' => 'Tipa',
                'parent' => $data['@id']
            ]
        ]);
        $this->assertResponseStatusCodeSame(201);

        $client->request('POST', '/api/categories', [
            'json' => [
                'name' => 'parent2'
            ]
        ]);
        $data = $client->getResponse()->toArray();
//        dd($data['@id']);
        $client->request('POST', '/api/categories', [
            'json' => [
                'name' => 'Dutch',
                'parent' => $data['@id'],
            ]
        ]);

        $this->assertResponseStatusCodeSame(201);
    }

    public function testUpdateCategory()
    {
        $client = self::createClient();
        $user = UserFactory::new()->create();
        $this->logInFactory($client, $user);
        $category = CategoriesFactory::new()->create();
        $category2 = CategoriesFactory::new()->create();

        $client->request('PUT', '/api/categories/'.$category->object()->getId(), [
            'json' => [
                'name' => 'ipa',
            ]
        ]);
        $this->assertResponseStatusCodeSame(403);

        $user->refresh();
        $user->setRoles(['ROLE_ADMIN']);
        $user->save();

        $this->logInFactory($client, $user);
        $client->request('PATCH', '/api/categories/'.$category->object()->getId(), [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => [
                'name' => 'ipa',
            ]
        ]);
        $this->assertResponseStatusCodeSame(200);

        $child = CategoriesFactory::new()->create(['parent' => $category]);
        $client->request('PUT', '/api/categories/'.$child->object()->getId(), [
            'json' => [
                'name' => 'Dipa',
                'parent' => '/api/categories/'.$category2->object()->getId()
            ]
        ]);
        $this->assertResponseStatusCodeSame(200);
//        $data = $client->getResponse()->toArray();
//        dd($data);

        $this->assertJsonContains(
            [
                'name' => 'Dipa',
                'parent' => [
                    '@id' => '/api/categories/'.$category2->object()->getId()
                ]
            ]
        );

        $client->request('PUT', '/api/categories/'.$child->object()->getId(), [
            'json' => [
                'name' => 'Dipa',
                'parent' => '/api/categories/10'
            ]
        ]);
        $this->assertResponseStatusCodeSame(400);
    }

    public function testGetCategories(){
        $client = self::createClient();
        $user = UserFactory::new()->create();
        $cat = CategoriesFactory::new()->create();
        $cat2 = CategoriesFactory::new()->create(['parent' => $cat]);
        $cat3 = CategoriesFactory::new()->create(['parent' => $cat]);
        $cat4 = CategoriesFactory::new()->create(['parent' => $cat]);

        $client->request('GET', '/api/categories');
        $this->assertResponseStatusCodeSame(200);

        $client->request('GET', '/api/categories/'.$cat2->object()->getId());
        $this->assertResponseStatusCodeSame(200);

        $this->logInFactory($client, $user);
        $client->request('GET', '/api/categories');
        $this->assertResponseStatusCodeSame(200);

        $client->request('GET', '/api/categories?parent=/api/categories/1');
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(
            [
               "hydra:totalItems" => 3,
            ]
        );

        $client->request('GET', '/api/categories/'.$cat2->object()->getId());
        $this->assertResponseStatusCodeSame(200);

        $user->refresh();
        $user->setRoles(['ROLE_ADMIN']);
        $user->save();

        $this->logInFactory($client, $user);
        $client->request('GET', '/api/categories');
        $this->assertResponseStatusCodeSame(200);

        $client->request('GET', '/api/categories/'.$cat2->object()->getId());
        $this->assertResponseStatusCodeSame(200);

    }
}