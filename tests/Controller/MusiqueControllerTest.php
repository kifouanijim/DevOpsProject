<?php

namespace App\Tests\Controller;

use App\Entity\Musique;
use App\Repository\MusiqueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class MusiqueControllerTest extends WebTestCase{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/musique/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(Musique::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Musique index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'musique[nom]' => 'Testing',
            'musique[auteur]' => 'Testing',
            'musique[dateDeSortie]' => 'Testing',
            'musique[album]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->repository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Musique();
        $fixture->setNom('My Title');
        $fixture->setAuteur('My Title');
        $fixture->setDateDeSortie('My Title');
        $fixture->setAlbum('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Musique');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Musique();
        $fixture->setNom('Value');
        $fixture->setAuteur('Value');
        $fixture->setDateDeSortie('Value');
        $fixture->setAlbum('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'musique[nom]' => 'Something New',
            'musique[auteur]' => 'Something New',
            'musique[dateDeSortie]' => 'Something New',
            'musique[album]' => 'Something New',
        ]);

        self::assertResponseRedirects('/musique/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getNom());
        self::assertSame('Something New', $fixture[0]->getAuteur());
        self::assertSame('Something New', $fixture[0]->getDateDeSortie());
        self::assertSame('Something New', $fixture[0]->getAlbum());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Musique();
        $fixture->setNom('Value');
        $fixture->setAuteur('Value');
        $fixture->setDateDeSortie('Value');
        $fixture->setAlbum('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/musique/');
        self::assertSame(0, $this->repository->count([]));
    }
}
