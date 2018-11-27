<?php

namespace App\Command;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\SetCookie;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DomCrawler\Crawler;

class ScrapCommand extends Command
{
    protected static $defaultName = 'app:scrap';

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            //->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            //->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);


        $client = new Client(array(
            'timeout' => 50,
            'verify' => false,
            'proxy' => 'http://10.100.0.248:8080',
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.102 Safari/537.36',

            ]
        ));

        $aContext = array(
            'http' => array(
                'proxy' => 'tcp://10.100.0.248:8080',
                'request_fulluri' => true,
            ),
        );
        $cxContext = stream_context_create($aContext);

        $url = "https://www.sarenza.com/chaussure-adidas-originals-homme";
        $response = $client->get($url);

        $html = $response->getBody();
        //$io->text($html);

        $crawler = new Crawler((string) $html);

        $pageElement = $crawler->filter('.mighty.price')->first();
        $prix = $pageElement->text();

        $io->text($prix);


        //Gestion Image
//        $pageElement = $crawler->filter('.h-card.col-3.float-left.pr-3');
//        $pageElement->each(function (Crawler $pageElements) use ($io, $cxContext) {
//
//            $img = $pageElements->filter('img')->first();
//            $imgsrc = $img->attr('src');
//
//            $reverse = strrev($imgsrc);
//            $tab = explode("/", $reverse);
//            $nameFile = strrev($tab[0]);
//            dump($nameFile);
//            file_put_contents('C:\xampp\htdocs\scraper\public\image\\'.$nameFile.'', file_get_contents($imgsrc, false, $cxContext));
//            $io->text($imgsrc);
//        });



        /*
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }
        */

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');
    }
}
