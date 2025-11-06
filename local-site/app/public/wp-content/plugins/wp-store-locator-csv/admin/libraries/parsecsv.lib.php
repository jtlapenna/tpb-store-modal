<?php

if ( !file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
    require __DIR__ . '/src/enums/AbstractEnum.php';
    require __DIR__ . '/src/enums/DatatypeEnum.php';
    require __DIR__ . '/src/enums/FileProcessingModeEnum.php';
    require __DIR__ . '/src/enums/SortEnum.php';
    require __DIR__ . '/src/extensions/DatatypeTrait.php';
    require __DIR__ . '/src/Csv.php';
} else {
    require __DIR__ . '/vendor/autoload.php';
}