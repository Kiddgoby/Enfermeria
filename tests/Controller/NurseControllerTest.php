<?php

namespace App\Tests\Controller;

// Small stub repository class to allow mocking a custom findOneByName method used in the controller.
class TestUserRepository extends \App\Repository\UserRepository
{
    // Avoid calling parent constructor which requires ManagerRegistry
    public function __construct() {}

    public function findOneByName($name)
    {
        return null;
    }
}

use App\Controller\NurseController;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class NurseControllerTest extends TestCase
{
    private function controller(): NurseController
    {
        $controller = new NurseController();
        $container = $this->createMock(\Psr\Container\ContainerInterface::class);
        // Ensure serializer is reported as unavailable so controller::json falls back to JsonResponse with raw data
        $container->method('has')->willReturn(false);
        $controller->setContainer($container);

        return $controller;
    }

    public function testFindByNameFound(): void
    {
        $user = new User();
        $user->setName('Alice')->setUser('alice_user')->setPassword('pw');
        $repo = $this->getMockBuilder(TestUserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findOneByName'])
            ->getMock();
        $repo->method('findOneByName')->with('Alice')->willReturn($user);

        $controller = $this->controller();
        $response = $controller->findByName('Alice', $repo);

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertSame('Alice', $data['name']);
        $this->assertSame('alice_user', $data['user']);
        $this->assertSame('pw', $data['password']);
    }

    public function testFindByNameNotFound(): void
    {
        $repo = $this->getMockBuilder(TestUserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findOneByName'])
            ->getMock();
        $repo->method('findOneByName')->with('Bob')->willReturn(null);

        $controller = $this->controller();
        $response = $controller->findByName('Bob', $repo);

        $this->assertEquals(404, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $data);
    }

    public function testGetAll(): void
    {
        $u1 = new User();
        $u1->setUser('u1')->setPassword('p1');
        $u2 = new User();
        $u2->setUser('u2')->setPassword('p2');
        $repo = $this->getMockBuilder(TestUserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findAll'])
            ->getMock();
        $repo->method('findAll')->willReturn([$u1, $u2]);

        $controller = $this->controller();
        $response = $controller->getAll($repo);

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertCount(2, $data);
        $this->assertSame('u1', $data[0]['user']);
        $this->assertSame('p2', $data[1]['password']);
    }

    public function testLoginSuccess(): void
    {
        $user = new User();
        $user->setUser('bob')->setPassword('secret');
        $repo = $this->getMockBuilder(TestUserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findOneBy'])
            ->getMock();
        $repo->method('findOneBy')->with(['user' => 'bob'])->willReturn($user);

        $content = json_encode(['user' => 'bob', 'password' => 'secret']);
        $request = Request::create('/nurse/login', 'POST', [], [], [], [], $content);
        $controller = $this->controller();
        $response = $controller->login($request, $repo);

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertSame('Login successful', $data['message']);
    }

    public function testLoginFail(): void
    {
        $repo = $this->getMockBuilder(TestUserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findOneBy'])
            ->getMock();
        $repo->method('findOneBy')->with(['user' => 'doesnotexist'])->willReturn(null);

        $content = json_encode(['user' => 'doesnotexist', 'password' => 'x']);
        $request = Request::create('/nurse/login', 'POST', [], [], [], [], $content);
        $controller = $this->controller();
        $response = $controller->login($request, $repo);

        $this->assertEquals(401, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $data);
    }

    public function testNewSuccess(): void
    {
        $repo = $this->getMockBuilder(TestUserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findOneBy'])
            ->getMock();
        $repo->method('findOneBy')->with(['user' => 'newuser'])->willReturn(null);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->once())->method('persist');
        $em->expects($this->once())->method('flush');

        $payload = ['user' => 'newuser', 'password' => 'pw', 'name' => 'New Name'];
        $request = Request::create('/nurse/new', 'POST', [], [], [], [], json_encode($payload));

    $controller = $this->controller();
        $response = $controller->new($request, $repo, $em);

        $this->assertEquals(201, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('message', $data);
        $this->assertSame('Nurse created successfully', $data['message']);
    }

    public function testNewMissingFields(): void
    {
        $repo = $this->getMockBuilder(TestUserRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $em = $this->createMock(EntityManagerInterface::class);

        $request = Request::create('/nurse/new', 'POST', [], [], [], [], json_encode(['user' => 'onlyuser']));
        $controller = $this->controller();
        $response = $controller->new($request, $repo, $em);

        $this->assertEquals(400, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $data);
    }

    public function testGetNurseByIdFound(): void
    {
        $user = new User();
        $user->setUser('uid')->setPassword('pw')->setName('Name');
        $repo = $this->getMockBuilder(TestUserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['find'])
            ->getMock();
        $repo->method('find')->with(42)->willReturn($user);

        $controller = $this->controller();
        $response = $controller->getNurseById(42, $repo);

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertSame('uid', $data['user']);
        $this->assertSame('Name', $data['name']);
    }

    public function testGetNurseByIdNotFound(): void
    {
        $repo = $this->getMockBuilder(TestUserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['find'])
            ->getMock();
        $repo->method('find')->with(99)->willReturn(null);

        $controller = $this->controller();
        $response = $controller->getNurseById(99, $repo);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testUpdateSuccess(): void
    {
        $user = new User();
        $user->setUser('old')->setPassword('oldpw')->setName('Old');
        $repo = $this->getMockBuilder(TestUserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['find'])
            ->getMock();
        $repo->method('find')->with(7)->willReturn($user);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->once())->method('persist')->with($user);
        $em->expects($this->once())->method('flush');

        $payload = ['user' => 'newuser', 'password' => 'newpw', 'name' => 'New'];
        $request = Request::create('/nurse/update/7', 'PUT', [], [], [], [], json_encode($payload));

    $controller = $this->controller();
        $response = $controller->update(7, $request, $repo, $em);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSame('newuser', $user->getUser());
        $this->assertSame('newpw', $user->getPassword());
    }

    public function testUpdateNotFound(): void
    {
        $repo = $this->getMockBuilder(TestUserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['find'])
            ->getMock();
        $repo->method('find')->with(123)->willReturn(null);

        $em = $this->createMock(EntityManagerInterface::class);
        $request = Request::create('/nurse/update/123', 'PUT', [], [], [], [], json_encode(['user' => 'x']));

        $controller = $this->controller();
        $response = $controller->update(123, $request, $repo, $em);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testDeleteSuccess(): void
    {
        $user = new User();
        $user->setUser('todel');
        $repo = $this->getMockBuilder(TestUserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['find'])
            ->getMock();
        $repo->method('find')->with(5)->willReturn($user);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->once())->method('remove')->with($user);
        $em->expects($this->once())->method('flush');
        $controller = $this->controller();
        $response = $controller->delete(5, $repo, $em);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testDeleteNotFound(): void
    {
        $repo = $this->getMockBuilder(TestUserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['find'])
            ->getMock();
        $repo->method('find')->with(999)->willReturn(null);

        $em = $this->createMock(EntityManagerInterface::class);

        $controller = $this->controller();
        $response = $controller->delete(999, $repo, $em);

        $this->assertEquals(404, $response->getStatusCode());
    }
}
