<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Tests\DatabaseTestCase;
use Doctrine\ORM\EntityManagerInterface;

final class PostControllerTest extends DatabaseTestCase
{
    public function testNewRequiresAuth(): void
    {
        // Anonymous users should be redirected to /login
        $this->client->request('GET', '/post/new');
        self::assertResponseRedirects('/login');
    }

    public function testCreatePostAndViewBySlug(): void
    {
        $em = static::getContainer()->get(EntityManagerInterface::class);

        // Create and persist a user
        $user = new User();
        $user->setEmail('demo@example.com');
        $user->setPassword(password_hash('demo_password', PASSWORD_BCRYPT));
        $em->persist($user);
        $em->flush();

        // Log in as the user
        $this->login('demo@example.com', 'demo_password');

        // Access the new post form
        $crawler = $this->client->request('GET', '/post/new');
        self::assertResponseIsSuccessful();

        // Submit the post form
        $form = $crawler->selectButton('Save')->form([
            'post[title]' => 'My First Blog Post',
            'post[content]' => 'Hello world, this is a test post.',
        ]);

        $this->client->submit($form);
        $this->client->followRedirect();

        // Check that the new post is displayed on the home page
        self::assertResponseIsSuccessful();
        self::assertStringContainsString('My First Blog Post', $this->client->getResponse()->getContent());

        // Find the post by slug and visit it
        $post = $em->getRepository(Post::class)->findOneBy(['title' => 'My First Blog Post']);
        self::assertNotNull($post);

        $this->client->request('GET', '/post/' . $post->getSlug());
        self::assertResponseIsSuccessful();
        self::assertStringContainsString(
            'Hello world, this is a test post.',
            $this->client->getResponse()->getContent()
        );
    }

    /**
     * Logs in a user by submitting the login form.
     */
    private function login(string $email, string $password): void
    {
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')->form([
            'email' => $email,
            'password' => $password,
        ]);
        $this->client->submit($form);
        $this->client->followRedirect();

        self::assertResponseIsSuccessful();
        self::assertStringContainsString('Logout', $this->client->getResponse()->getContent());
    }
}
