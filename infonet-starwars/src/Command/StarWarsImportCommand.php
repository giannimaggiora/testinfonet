<?php

namespace App\Command;

use App\Entity\Character;
use App\Entity\Movie;
use App\Entity\MovieCharacter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpClient\HttpClient;

#[AsCommand(
    name: 'starwars:import',
    description: 'Import Star Wars characters and movies from SWAPI',
)]
class StarWarsImportCommand extends Command
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->setHelp('This command allows you to import Star Wars characters and movies from SWAPI...');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $httpClient = HttpClient::create();

        // Importar personajes
        $charactersResponse = $httpClient->request('GET', 'https://swapi.dev/api/people/');
        $charactersData = $charactersResponse->toArray();
        $characterMap = [];

        foreach (array_slice($charactersData['results'], 0, 30) as $characterData) {
            $character = new Character();
            $character->setName($characterData['name']);
            $character->setMass($characterData['mass']);
            $character->setHeight($characterData['height']);
            $character->setGender($characterData['gender']);
            $character->setPicture(''); // Deberás proporcionar las imágenes manualmente más adelante

            $this->entityManager->persist($character);
            $this->entityManager->flush(); // Guardar para obtener el ID
            $characterMap[$characterData['url']] = $character->getId();
        }

        // Importar películas
        $moviesResponse = $httpClient->request('GET', 'https://swapi.dev/api/films/');
        $moviesData = $moviesResponse->toArray();

        foreach ($moviesData['results'] as $movieData) {
            $movie = new Movie();
            $movie->setName($movieData['title']);

            $this->entityManager->persist($movie);
            $this->entityManager->flush(); // Guardar para obtener el ID

            // Relacionar personajes con películas
            foreach ($movieData['characters'] as $characterUrl) {
                if (isset($characterMap[$characterUrl])) {
                    $movieCharacter = new MovieCharacter();
                    $movieCharacter->setMovie($movie);
                    $movieCharacter->setCharacter($this->entityManager->getReference(Character::class, $characterMap[$characterUrl]));
                    $movieCharacter->setPicture(''); // Configura la imagen si es necesario

                    $this->entityManager->persist($movieCharacter);
                }
            }
        }

        $this->entityManager->flush();

        $io->success('Data imported successfully!');

        return Command::SUCCESS;
    }
}
