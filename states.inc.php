  
<?php

/**
 * ------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * Linko implementation : © <Your name here> <Your email address here>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 * 
 * states.inc.php
 *
 * Linko game states description
 *
 */
/*
  Game state machine is a tool used to facilitate game developpement by doing common stuff that can be set up
  in a very easy way from this configuration file.
  Please check the BGA Studio presentation about game state to understand this, and associated documentation.
  Summary:
  States types:
  _ activeplayer: in this type of state, we expect some action from the active player.
  _ multipleactiveplayer: in this type of state, we expect some action from multiple players (the active players)
  _ game: this is an intermediary state where we don't expect any actions from players. Your game logic must decide what is the next game state.
  _ manager: special type for initial and final state
  Arguments of game states:
  _ name: the name of the GameState, in order you can recognize it on your own code.
  _ description: the description of the current game state is always displayed in the action status bar on
  the top of the game. Most of the time this is useless for game state with "game" type.
  _ descriptionmyturn: the description of the current game state when it's your turn.
  _ type: defines the type of game states (activeplayer / multipleactiveplayer / game / manager)
  _ action: name of the method to call when this game state become the current game state. Usually, the
  action method is prefixed by "st" (ex: "stMyGameStateName").
  _ possibleactions: array that specify possible player actions on this step. It allows you to use "checkAction"
  method on both client side (Javacript: this.checkAction) and server side (PHP: self::checkAction).
  _ transitions: the transitions are the possible paths to go from a game state to another. You must name
  transitions in order to use transition names in "nextState" PHP method, and use IDs to
  specify the next game state for each transition.
  _ args: name of the method to call to retrieve arguments for this gamestate. Arguments are sent to the
  client side to be used on "onEnteringState" or to set arguments in the gamestate description.
  _ updateGameProgression: when specified, the game progression is updated (=> call to your getGameProgression
  method).
 */

//    !! It is not a good idea to modify this file when a game is running !!
//if (!defined("ST_BEGIN_GAME")) {
//    define("ST_BEGIN_GAME", 1);
//    define("ST_END_GAME", 99);
//
//    //-- start game
//    define("ST_START_OF_TURN", 2);
//
//    //-- Player Action
//    define("ST_PLAY_CARD", 10);
//
//    //-- STATE RESOLVER
//    define("ST_RESOLVE_STATE", 90);
//}


$machinestates = [
    /*
     * BGA framework initial state. Do not modify.
     */
    ST_BEGIN_GAME => [
        'name' => 'gameSetup',
        'description' => '',
        'type' => 'manager',
        'action' => 'stGameSetup',
        'transitions' => [
            '' => ST_START_OF_TURN,
        ],
    ],
    /*
     * Start of a turn : trigger new player turn
     */
    ST_START_OF_TURN => [
        'name' => 'startOfTurn',
        'description' => '',
        'type' => 'game',
        'action' => 'stStartOfTurn',
        'transitions' => [
            '' => ST_RESOLVE_STATE,
        ],
    ],
    ST_END_OF_TURN => [
        'name' => 'endOfTurn',
        'description' => '',
        'type' => 'game',
        'action' => 'stEndOfTurn',
        'transitions' => [
            '' => ST_RESOLVE_STATE,
        ],
    ],
    ST_RESOLVE_STATE => [
        'name' => 'resolveStack',
        'description' => '',
        'type' => 'game',
        'action' => 'stResolveState',
        'transitions' => [],
    ],
    //-- PLAYER ACTIONS
    ST_PLAYER_PLAY_NUMBER => [
        "name" => "playNumber",
        "description" => clienttranslate('${actplayer} can play card(s)'),
        "descriptionmyturn" => clienttranslate('${you} can play card(s)'),
        "type" => "activeplayer",
        "args" => "argPlayCards",
        "action" => "stPlayCards",
        "possibleactions" => ["playCards"]
    ],
    ST_PLAYER_TAKE_COLLECTION => [
        "name" => "takeCollection",
        "description" => clienttranslate('${actplayer} can steal cards collection(s)'),
        "descriptionmyturn" => clienttranslate('${you} can steal cards collection(s)'),
        "type" => "activeplayer",
        "args" => "argStealCollection",
        "action" => "stStealCollection",
        "possibleactions" => ["stealCollection","discardCollection"]
    ],
    ST_PLAYER_DRAW => [
        "name" => "playerDraw",
        "description" => clienttranslate('${actplayer} should draw ${numberOfCard} card(s)'),
        "descriptionmyturn" => clienttranslate('${you} should draw ${numberOfCard} card(s)'),
        "type" => "activeplayer",
        "args" => "argDrawCards",
        "action" => "stDrawCard",
        "possibleactions" => ["actDrawCards"]
    ],
    /*
     * BGA framework final state. Do not modify.
     */
    ST_END_GAME => [
        'name' => 'gameEnd',
        'description' => clienttranslate('End of game'),
        'type' => 'manager',
        'action' => 'stGameEnd',
        'args' => 'argGameEnd',
    ]
];

//$machinestates = array(
//
//    // The initial state. Please do not modify.
//    1 => array(
//        "name" => "gameSetup",
//        "description" => "",
//        "type" => "manager",
//        "action" => "stGameSetup",
//        "transitions" => array( "" => 2 )
//    ),
//    
//    // Note: ID=2 => your first state
//
//    2 => array(
//    		"name" => "playerTurn",
//    		"description" => clienttranslate('${actplayer} must play a card or pass'),
//    		"descriptionmyturn" => clienttranslate('${you} must play a card or pass'),
//    		"type" => "activeplayer",
//    		"possibleactions" => array( "playCard", "pass" ),
//    		"transitions" => array( "playCard" => 2, "pass" => 2 )
//    ),
//    
///*
//    Examples:
//    
//    2 => array(
//        "name" => "nextPlayer",
//        "description" => '',
//        "type" => "game",
//        "action" => "stNextPlayer",
//        "updateGameProgression" => true,   
//        "transitions" => array( "endGame" => 99, "nextPlayer" => 10 )
//    ),
//    
//    10 => array(
//        "name" => "playerTurn",
//        "description" => clienttranslate('${actplayer} must play a card or pass'),
//        "descriptionmyturn" => clienttranslate('${you} must play a card or pass'),
//        "type" => "activeplayer",
//        "possibleactions" => array( "playCard", "pass" ),
//        "transitions" => array( "playCard" => 2, "pass" => 2 )
//    ), 
//*/    
//   
//    // Final state.
//    // Please do not modify (and do not overload action/args methods).
//    99 => array(
//        "name" => "gameEnd",
//        "description" => clienttranslate("End of game"),
//        "type" => "manager",
//        "action" => "stGameEnd",
//        "args" => "argGameEnd"
//    )
//
//);