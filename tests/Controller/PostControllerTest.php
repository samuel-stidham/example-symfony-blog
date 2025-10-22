<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Tests\DatabaseTestCase;

final class PostControllerTest extends DatabaseTestCase
{
    public function testNewRequiresAuth(): void
    {
        $this->client->request('GET', '/post/new');
        self::assertResponseRedirects('/login');
    }

    public function testCreatePostAndViewBySlug(): void
    {
        // Use the typed EM provided by DatabaseTestCase
        $em = $this->em;

        // Create and persist a user
        $user = (new User())
            ->setEmail('demo@example.com')
            ->setPassword(password_hash('demo_password', PASSWORD_BCRYPT));

        $em->persist($user);
        $em->flush();

        // Log in as the user
        $this->login('demo@example.com', 'demo_password');

        // Access the new post form
        $crawler = $this->client->request('GET', '/post/new');
        self::assertResponseIsSuccessful();

        // Submit the post form
        $form = $crawler->selectButton('Save')->form([
            'post[title]'   => 'My First Blog Post',
            'post[content]' => 'Hello world, this is a test post.',
        ]);
        $this->client->submit($form);

        // Ensure we got a redirect, then follow it
        self::assertResponseRedirects();
        $this->client->followRedirect();

        // Check that the new post is displayed on the home page
        self::assertResponseIsSuccessful();
        self::assertStringContainsString(
            'My First Blog Post',
            (string) $this->client->getResponse()->getContent() // cast fixes string|false
        );

        // Find the post by slug and visit it
        /** @var Post|null $post */
        $post = $em->getRepository(Post::class)->findOneBy(['title' => 'My First Blog Post']);
        self::assertInstanceOf(Post::class, $post);

        $this->client->request('GET', '/post/' . $post->getSlug());
        self::assertResponseIsSuccessful();
        self::assertStringContainsString(
            'Hello world, this is a test post.',
            (string) $this->client->getResponse()->getContent() // cast fixes string|false
        );
    }

    private function login(string $email, string $password): void
    {
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')->form([
            'email' => $email,
            'password' => $password,
        ]);
        $this->client->submit($form);
        self::assertResponseRedirects();
        $this->client->followRedirect();

        self::assertResponseIsSuccessful();
        self::assertStringContainsString('Logout', (string) $this->client->getResponse()->getContent());
    }
}
