<?php

namespace Uniqoders\Game\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Uniqoders\Game\Config\Config;

class GameCommand extends Command
{
    private $config;

    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('game')
            ->setDescription('New game: you vs computer')
            ->addArgument('name', InputArgument::OPTIONAL, 'what is your name?', 'Player 1');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->config = new Config();
        $output->write(PHP_EOL . 'Made with ♥ by Uniqoders.' . PHP_EOL . PHP_EOL);

        $player_name = $input->getArgument('name');

        $io = new SymfonyStyle($input, $output);
        $io->title('Bienvenido '.$player_name.', que gane el mejor!');

        //Inicializo variables de configuración
        $players    = $this->config->players($player_name);
        $game_type  = $this->config->gameType();
        $rondas     = $this->config->getRondas();

        $ask = $this->getHelper('question');

        // Se pregunta por el tipo de juego
        $question = new ChoiceQuestion('Selecciona el tipo de juego', $game_type, 1);
        $question->setErrorMessage('Elige una opción valida.');
        $seleccionado = $ask->ask($input, $output, $question);

        // Se pregunta por la cantidad de rondas
        $question = new ChoiceQuestion('Selecciona la cantidad de rondas', $rondas, 1);
        $question->setErrorMessage('Elige una opción valida.');
        $max_round = $ask->ask($input, $output, $question);

        // Se configuran las armas y las reglas según el juego seleccionado
        $weapons = $this->config->weapons($seleccionado);
        $rules   = $this->config->rules($seleccionado);

        $round = 1;

        do {
            // User selection
            $question = new ChoiceQuestion('Selecciona tu arma', array_values($weapons), 1);
            $question->setErrorMessage('Opción no valida.');

            $user_weapon = $ask->ask($input, $output, $question);
            $io->section('Seleccionaste: ' . $user_weapon);
            $user_weapon = array_search($user_weapon, $weapons);

            // Computer selection
            $computer_weapon = array_rand($weapons);
            $io->section('Tu oponente eligió: ' . $weapons[$computer_weapon]);

            //Evaluo al ganador
            $respuesta = $this->getGanador($user_weapon, $computer_weapon, $players, $rules);
            $io->title($respuesta[1]);
            $players = $respuesta[0];

            //Evaluo la condición de Si un jugador llega a 3 rondas ganadas, la partida finaliza
            if ($players['player']['stats']['victory'] === $this->config::RONDAS_GANAR || $players['computer']['stats']['victory'] === $this->config::RONDAS_GANAR) {
                break;
            }

            $round++;
        } while ($round <= $max_round);

        // Display stats
        $stats = $players;

        $stats = array_map(function ($player) {
            return [$player['name'], $player['stats']['victory'], $player['stats']['draw'], $player['stats']['defeat']];
        }, $stats);

        $table = new Table($output);
        $table
            ->setHeaders(['Player', 'Victory', 'Draw', 'Defeat'])
            ->setRows($stats);

        $table->render();

        return 0;
    }

    /**
     * Funcion para evaluar al ganador de la ronda
     */
    public function getGanador($user_weapon, $computer_weapon, $players, $rules ){
        if ($user_weapon === $computer_weapon) {
            $players['player']['stats']['draw']++;
            $players['computer']['stats']['draw']++;
            $mensaje = 'Empate!';
        } else if (in_array($computer_weapon, $rules[$user_weapon])) {
            $players['player']['stats']['victory']++;
            $players['computer']['stats']['defeat']++;
            $mensaje = ' Ganaste :)';
        } else {
            $players['player']['stats']['defeat']++;
            $players['computer']['stats']['victory']++;
            $mensaje = 'Ganó tu oponente :(';
        }
        return array($players, $mensaje);
    }
}
