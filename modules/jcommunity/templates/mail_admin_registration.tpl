{* this is a template used when it does not exist for a specific language
translated templates should be into tpl/xx_YY/ directories
example: jcommunity/tpl/it_IT/mail_admin_registration.tpl *}
{meta Subject 'Activating your account on the web site '.$domain_name}
<p>Hello,</p>

<p>The administrator of the web site <a href="{$website_uri}" class="notexpandlink">{$domain_name}</a>
have been created an account for you, with the login <em>"{$user->login}"</em>.</p>

<p>Before to use your account, you should confirm your registration
by <a href="{$confirmation_link}">clicking on this link</a>,
and then indicating a new password.</p>

<p>See you on {$domain_name}!</p>

