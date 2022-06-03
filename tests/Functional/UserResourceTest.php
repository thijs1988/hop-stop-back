<?php


namespace App\Tests\Functional;

use App\Entity\User;
use App\Factory\UserFactory;
use App\Test\CustomApiTestCase;

class UserResourceTest extends CustomApiTestCase
{
    public function testCreateUser()
    {
        $client = self::createClient();

        $client->request('POST', '/api/users', [
            'json' => [
                'email' => 'thijs@gmail.com',
                'username' => 'beerplease',
                'password' => 'foo',
                'name' => 'thijs',
                'phoneNumber' => '06-12873056',
                'street' => 'sterkenhoeve 7',
                'postbox' => '5122HB',
                'place' => 'Rijen',
                'age' => 18
            ]
        ]);
        $this->assertResponseStatusCodeSame(201);

        $user = UserFactory::repository()->findOneBy(['email' => 'thijs@gmail.com']);
        $this->assertNotNull($user);
        $this->assertJsonContains([
            '@id' => '/api/users/'.$user->getId()
        ]);

        $this->logInFactory($client, 'thijs@gmail.com', 'foo');
    }

    public function testUpdateUser(){
        $client = self::createClient();
        $user = UserFactory::new()->create();
        $this->logInFactory($client, $user);

        $client->request('PUT', '/api/users/'.$user->getId(),  [
            'json' => [
                'username' => 'newusername',
                'email' => 'newusername@gmail.com',
                'roles' => ['ROLE_ADMIN'], //will be ignored
            ]
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
                'username' => 'newusername',
                'email' => 'newusername@gmail.com'
        ]);

        $user->refresh();
        $this->assertEquals(['ROLE_USER'], $user->getRoles());

        $client->request('PATCH', '/api/users/'.$user->getId(),  [
            'headers' => ["Content-Type" => "application/merge-patch+json"],
            'json' => [
                'username' => 'newusername2',
            ]
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'username' => 'newusername2',
        ]);
    }

    public function testGetUser(){
        $client = self::createClient();
        $user = UserFactory::new()->create([
            'phoneNumber' => '555.123.4567',
            'username' => 'cheesehead'
        ]);
        $client->request('GET', '/api/users/'.$user->getId());
        $this->assertResponseStatusCodeSame(401);

        $client->request('GET', '/api/users/');
        $this->assertResponseStatusCodeSame(401);

        $authenticatedUser = UserFactory::new()->create();
        $this->logInFactory($client, $authenticatedUser);

        $client->request('GET', '/api/users/'.$user->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'username' => $user->getUsername()
        ]);

        $data = $client->getResponse()->toArray();
        $this->assertArrayNotHasKey('phoneNumber', $data);
        $this->assertJsonContains([
            'isMe' => false,
        ]);

        // refresh the user & elevate
        $user->refresh();
        $user->setRoles(['ROLE_ADMIN']);
        $user->save();
        $this->logInFactory($client, $user);

        $client->request('GET', '/api/users/'.$user->getId());
        $this->assertJsonContains([
            'phoneNumber' => '555.123.4567',
            'isMe' => true,
        ]);
    }
}