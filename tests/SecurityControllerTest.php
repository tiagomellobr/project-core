<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLoadLoginPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertFormValue('form', 'login[email]', '');
        $this->assertFormValue('form', 'login[password]', '');
    }

    public function testInvalidLogIn()
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $client->submitForm('submit', [
            'login[email]' => 'doesNotExist@mail.com',
            'login[password]' => 'password',
        ]);
        self::assertResponseRedirects('/');
        $client->followRedirect();

        self::assertSelectorTextContains('.alert-danger', 'Invalid credentials.');
    }

    public function testValidLogIn()
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $client->submitForm('submit', [
            'login[email]' => 'testuser@mail.com',
            'login[password]' => 'Us3rP4s5',
        ]);
        self::assertResponseRedirects('/app');
        $client->followRedirect();

        self::assertSelectorNotExists('.alert-danger');
    }
}
