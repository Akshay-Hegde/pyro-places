<?php

/**
 * This module was built specifically with Danish volleyball leagues in mind,
 * but the abstraction should be distant enough to allow for practically any
 * sport that has a generic tournament structure, different leagues (or series,
 * or .... you know), players, coaches and a team description.
 *
 * @author Johannes L. Borresen
 * @website http://the.homestead.dk
 * @package sports
 **/

$lang['sports:sports']              =   'Sport';

$lang['sports:leagues']             =   'Serier';

// Specific for sports (well, that's general for all...)

// Specific for admin.
$lang['sports:admin:create-team']	=	'Tilføj hold';
$lang['sports:admin:create-league']	=	'Tilføj serie';
$lang['sports:admin:create-coach']	=	'Tilføj træner';

$lang['sports:admin:leagues']		=	'Serier';
$lang['sports:admin:teams']			=	'Hold';
$lang['sports:admin:coaches']		=	'Trænere';

// Specific for teams
$lang['sports:teams']               =   'Hold';
$lang['sports:team:id']             =   'Id';
$lang['sports:team:name']           =   'Navn';
$lang['sports:team:slug']           =   'Forkortelse';
$lang['sports:team:description']    =   'Beskrivelse';
$lang['sports:team:head-coach']     =   'Hovedtræner';
$lang['sports:team:view']           =   'Detaljer';

$lang['sports:admin:team:edit']     =   'Rediger';

$lang['sports:team:delete']         =   'Slet';

$lang['sports:team:league']			=	'Serie';

// Specific for leagues
$lang['sports:league-name']         =   'Navn';
$lang['sports:league-create']       =   'Opret';

?>
