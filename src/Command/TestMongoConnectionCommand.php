<?php

namespace App\Command;

use MongoDB\Client;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:test:mongo',
    description: 'Teste la connexion à MongoDB et la présence de l\'extension'
)]
class TestMongoConnectionCommand extends Command
{
    public function __construct(private Client $mongoClient, private string $dbName, private string $collectionName)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // 1. Vérifier si l'extension MongoDB PHP est chargée
        if (!extension_loaded('mongodb')) {
            $output->writeln('<error>❌ L\'extension MongoDB PHP n\'est pas activée !</error>');
            return Command::FAILURE;
        }
        $output->writeln('<info>✅ L\'extension MongoDB PHP est bien activée.</info>');

        try {
            // 2. Tester la connexion
            $output->writeln('<info>⏳ Tentative de connexion à MongoDB...</info>');
            $db = $this->mongoClient->selectDatabase($this->dbName);

            // 3. Vérifier la collection
            $collection = $db->selectCollection($this->collectionName);
            $count = $collection->countDocuments();

            $output->writeln("<info>✅ Connexion réussie à la base '{$this->dbName}', collection '{$this->collectionName}'</info>");
            $output->writeln("<comment>📦 Nombre de documents : {$count}</comment>");
        } catch (\Exception $e) {
            $output->writeln('<error>❌ Erreur de connexion à MongoDB : ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}