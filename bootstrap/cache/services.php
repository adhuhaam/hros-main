<?php return array (
  'providers' => 
  array (
    0 => 'Laravel\\Sail\\SailServiceProvider',
    1 => 'Laravel\\Tinker\\TinkerServiceProvider',
    2 => 'Carbon\\Laravel\\ServiceProvider',
    3 => 'NunoMaduro\\Collision\\Adapters\\Laravel\\CollisionServiceProvider',
    4 => 'Termwind\\Laravel\\TermwindServiceProvider',
    5 => 'Yajra\\DataTables\\ButtonsServiceProvider',
    6 => 'Yajra\\DataTables\\EditorServiceProvider',
    7 => 'Yajra\\DataTables\\FractalServiceProvider',
    8 => 'Yajra\\DataTables\\HtmlServiceProvider',
    9 => 'Yajra\\DataTables\\DataTablesServiceProvider',
  ),
  'eager' => 
  array (
    0 => 'Carbon\\Laravel\\ServiceProvider',
    1 => 'NunoMaduro\\Collision\\Adapters\\Laravel\\CollisionServiceProvider',
    2 => 'Termwind\\Laravel\\TermwindServiceProvider',
    3 => 'Yajra\\DataTables\\ButtonsServiceProvider',
    4 => 'Yajra\\DataTables\\EditorServiceProvider',
    5 => 'Yajra\\DataTables\\FractalServiceProvider',
    6 => 'Yajra\\DataTables\\HtmlServiceProvider',
    7 => 'Yajra\\DataTables\\DataTablesServiceProvider',
  ),
  'deferred' => 
  array (
    'Laravel\\Sail\\Console\\InstallCommand' => 'Laravel\\Sail\\SailServiceProvider',
    'Laravel\\Sail\\Console\\PublishCommand' => 'Laravel\\Sail\\SailServiceProvider',
    'command.tinker' => 'Laravel\\Tinker\\TinkerServiceProvider',
  ),
  'when' => 
  array (
    'Laravel\\Sail\\SailServiceProvider' => 
    array (
    ),
    'Laravel\\Tinker\\TinkerServiceProvider' => 
    array (
    ),
  ),
);