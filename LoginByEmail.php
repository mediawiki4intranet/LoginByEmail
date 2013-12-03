<?php

/**
 * Copyright (C) 2013+ Vitaliy Filippov <vitalif at mail.ru>
 * http://wiki.4intra.net/LoginByEmail
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 * http://www.gnu.org/copyleft/gpl.html
 */

if (!defined('MEDIAWIKI'))
{
    die('This is an extension to MediaWiki software and cannot be used standalone');
}

$wgLoginByEmailDiscloseExistence = false;

$wgExtensionCredits['other'][] = array(
    'name' => 'LoginByEmail',
    'author' => 'VitaliyFilippov',
    'version' => '2013-12-03',
    'url' => 'http://wiki.4intra.net/LoginByEmail',
    'description' => 'Allows to log in using the e-mail address, not the login name',
);

$wgExtensionMessagesFiles[] = __DIR__.'/LoginByEmail.i18n.php';
$wgHooks['LoginGetUser'][] = 'LoginByEmail_GetUser';
$wgHooks['UserLoginForm'][] = 'LoginByEmail_Form';

function LoginByEmail_GetUser($username, $password, &$user)
{
    global $wgLoginByEmailDiscloseExistence;
    $dbr = wfGetDB(DB_SLAVE);
    $res = $dbr->select(
        'user', '*', array('user_email' => $username, 'user_email_authenticated IS NOT NULL'),
        __METHOD__, array('ORDER BY' => 'user_id')
    );
    foreach ($res as $row)
    {
        $u = User::newFromRow($row);
        if ($u->checkPassword($password))
        {
            $user = $u;
            break;
        }
        elseif ($wgLoginByEmailDiscloseExistence)
        {
            $user = $u;
        }
    }
    return true;
}

class LoginByEmail_TranslatorHack extends MediaWiki_I18N
{
    function translate($value)
    {
        if ($value == 'yourname')
        {
            $value = 'loginbyemail-yourname';
        }
        return parent::translate($value);
    }
}

function LoginByEmail_Form(&$template)
{
    $template->setTranslator(new LoginByEmail_TranslatorHack());
    return true;
}
