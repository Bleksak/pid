<?php

namespace App\Command;

use App\Actions\PIDActions;
use GuzzleHttp\Client;
use Nette\Utils\Json;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'fetch')]
final class FetchPIDCommand extends Command
{
    public function __construct(
        private readonly string $pointsOfSaleUrl,
        private readonly PIDActions $pidActions,
    ) {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $guzzleOptions = [
            'headers' => [
                'Accept' => 'application/json',
            ],
        ];

        $client = new Client([]);

        $response = $client->get($this->pointsOfSaleUrl, $guzzleOptions);
        if ($response->getStatusCode() !== 200) {
            $output->writeln(sprintf('Error fetching points of sale: %s', $response->getStatusCode()));
            return Command::FAILURE;
        }

        $pointsOfSale = Json::decode($response->getBody(), true);

        $this->pidActions->insertFromJson($pointsOfSale);

        return Command::SUCCESS;
    }
}
