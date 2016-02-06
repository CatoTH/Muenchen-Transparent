<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('Check that an Antrag with a Vorgang has the correct "Verwandte Seiten"');
$I->amOnPage('/antraege/2');
$I->see('Antrag mit verwandten Seiten');

$I->seeLink('Das Dokument zum Antrag mit verwandten Seiten', '/dokumente/2');

$I->see('Verwandte Seiten');
$I->seeLink('Ein verwandter Antrag', '/antraege/3');
$I->dontSeeLink('Ein verwandtes Dokument', '/dokumente/1'); // 
$I->dontSeeLink('Antrag mit verwandten Seiten', '/antraege/2');
$I->dontSee('Das Dokument zum Antrag mit verwandten Seiten', '#verwandte_seiten');