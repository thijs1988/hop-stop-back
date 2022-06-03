<?php


namespace App\Tests\Functional;



use App\Factory\CartItemsFactory;
use App\Factory\ProductsFactory;
use App\Factory\UserFactory;
use App\Test\CustomApiTestCase;


class CartResourceTest extends CustomApiTestCase
{
    public function testCreateCart()
    {
        $client = self::createClient();

        $client->request('POST', '/api/carts', [
            'json' => [],
        ]);
        $this->assertResponseStatusCodeSame(401);

        $authenticatedUser = UserFactory::new()->create();
        $this->logInFactory($client, $authenticatedUser);
        $otherUser = UserFactory::new()->create();
        $otherUser2 = UserFactory::new()->create();

        $client->request('POST', '/api/carts', [
            'json' => [],
        ]);
        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            'cartOwner' => '/api/users/'.$authenticatedUser->getId()
        ]);

        $product = ProductsFactory::new()->create();
        $product2 = ProductsFactory::new()->create();
        $product3 = ProductsFactory::new()->create();


        $client->request('POST', '/api/carts', [
            'json' => [
                'cartProducts' => [
                    '/api/products/'.$product->getId(),
                    '/api/products/'.$product2->getId(),
                ],
                'cartOwner' => '/api/users/'.$otherUser->getId()
            ]
        ]);
        $this->assertResponseStatusCodeSame(400, 'not passing correct owner');

        $client->request('POST', '/api/carts', [
            'json' => [
                'cartProducts' => [
                    '/api/products/'.$product->getId(),
                    '/api/products/'.$product3->getId(),
                ],
                'cartOwner' => '/api/users/'.$authenticatedUser->getId()
            ]
        ]);
        $this->assertResponseStatusCodeSame(400, 'Owner has to be unique');

        $this->logInFactory($client, $otherUser);
        $client->request('POST', '/api/carts', [
            'json' => [
                'cartProducts' => [
                    '/api/products/'.$product->getId(),
                    '/api/products/'.$product2->getId(),
                ],
                'cartOwner' => '/api/users/'.$otherUser->getId()
            ]
        ]);

        $this->logInFactory($client, $otherUser2);
        $client->request('POST', '/api/carts', [
            'json' => [
                'cartProducts' => [
                    '/api/products/'.$product->getId(),
                    '/api/products/'.$product2->getId(),
                ]
            ],
        ]);
        $this->assertResponseStatusCodeSame(201);
    }

    public function testUpdateCart()
    {
        $client = self::createClient();
        $user2 = UserFactory::new()->create();

        $product = ProductsFactory::new()->create();
        $product2 = ProductsFactory::new()->create();
        $product3 = ProductsFactory::new()->create();

        $authenticatedUser = UserFactory::new()->create();
        $this->logInFactory($client, $authenticatedUser);
        $client->request('POST', '/api/carts', [
            'json' => [
                'products' => [
                    '/api/products/'.$product->getId(),
                    '/api/products/'.$product2->getId(),
                ],
                'cartOwner' => '/api/users/'.$authenticatedUser->getId()
            ]
        ]);
        $this->assertResponseStatusCodeSame(201);
        $data = $client->getResponse()->toArray();

        $this->logInFactory($client, $user2);
        $client->request('PUT', $data['@id'], [
            'json' => [
                'products' => [
                    '/api/products/'.$product->getId(),
                    '/api/products/'.$product3->getId(),
                ],
                'cartOwner' => '/api/users/'.$user2->getId()
            ]
        ]);
        $this->assertResponseStatusCodeSame(403);
//        var_dump($client->getResponse()->getContent(false));
        $this->logInFactory($client, $authenticatedUser);
        $client->request('PUT', $data['@id'], [
            'json' => ['products' => [
                '/api/products/'.$product->getId(),
                '/api/products/'.$product3->getId(),
                ]
            ]
        ]);
        $this->assertResponseStatusCodeSame(200);
    }

    public function testGetCartCollection()
    {
        $client = self::createClient();
        $user1 = UserFactory::new()->create();
        $user2 = UserFactory::new()->create();
        $user3 = UserFactory::new()->create();

        $product = ProductsFactory::new()->create();
        $product2 = ProductsFactory::new()->create();

        $cart = CartItemsFactory::new()->create();

        $client->request('GET', '/api/carts/'.$cart->object()->getId());
        $this->assertResponseStatusCodeSame(401);

        $this->logInFactory($client, $user1);
        $client->request('POST', '/api/carts', [
            'json' => [
                'cartProducts' => [
                    '/api/products/'.$product->getId(),
                    '/api/products/'.$product2->getId(),
                ],
                'cartOwner' => '/api/users/'.$user1->getId()
            ]
        ]);

        $this->logInFactory($client, $user2);
        $client->request('POST', '/api/carts', [
            'json' => [
                'cartProducts' => [
                    '/api/products/'.$product->getId(),
                    '/api/products/'.$product2->getId(),
                ],
                'paid' => true,
                'cartOwner' => '/api/users/'.$user2->getId()
            ]
        ]);

        $this->logInFactory($client, $user3);
        $client->request('POST', '/api/carts', [
            'json' => [
                'cartProducts' => [
                    '/api/products/'.$product->getId()
                ],
                'cartOwner' => '/api/users/'.$user3->getId()
            ]
        ]);

        $user3->refresh();
        $user3->save();
        $this->logInFactory($client, $user3);

        $client->request('GET', '/api/carts/'.$user3->getCartItems()->getId());
        $this->assertResponseStatusCodeSame(200);

        $client->request('GET', '/api/carts');
        $this->assertResponseStatusCodeSame(403);

        $user3->refresh();
        $user3->setRoles(['ROLE_ADMIN']);
        $user3->save();
        $this->logInFactory($client, $user3);
        $client->request('PATCH', '/api/carts/'.$user3->getCartItems()->getId(), [
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
                'json' => [
                    'paid' => true,
                ]
        ]);
        $this->assertResponseStatusCodeSame(200);

        $client->request('DELETE', '/api/users/'.$cart->getCartOwner()->getId());
        $this->assertResponseStatusCodeSame(204);

        $client->request('GET', '/api/carts');
        $this->assertJsonContains(['hydra:totalItems' => 3]);

        $client->request('GET', '/api/carts?createdAt[after]=2020-12-12');
        $this->assertJsonContains(['hydra:totalItems' => 3]);

        $client->request('GET', '/api/carts?expireDate[after]=2020-12-12');
        $this->assertJsonContains(['hydra:totalItems' => 3]);

        $client->request('GET', '/api/carts?cartOwner=/api/users/1');
        $this->assertJsonContains(['hydra:totalItems' => 1]);

        $client->request('GET', '/api/carts?paid=0');
        $this->assertJsonContains(['hydra:totalItems' => 2]);

        $client->request('GET', '/api/carts?shipped=1');
        $this->assertJsonContains(['hydra:totalItems' => 0]);

    }
}