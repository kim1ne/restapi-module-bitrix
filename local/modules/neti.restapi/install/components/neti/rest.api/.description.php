<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
    "NAME" => 'REST API',
    "DESCRIPTION" => 'REST API компонент',
    "ICON" => "/images/news_list.gif",
    "PATH" => array(
		"ID" => "neti",
    	"NAME" => 'neti',
    	"SORT" => 10,
		"CHILD" => array(
			"ID" => "auth",
			"NAME" => 'REST API',
			"SORT" => 10,
		),
    ),
);
