{meta Subject 'Reset of your password on the web site '.$domain_name}
<p>Hello,</p>

<p>The administrator of the web site <a href="{$website_uri}" class="notexpandlink">{$domain_name}</a>
have been sent this email to you so you can change your password for the account <em>"{$user->login}"</em>.</p>

<p>If you want to confirm the change, you should <a href="{$confirmation_link}">click on this link</a>.</p>

<p>You could then set a new password for your account. The link is valid 48h.</p>

<p>If this request is an error or you don't want to confirm, ignore this mail,
and your password won't be changed.</p>


<p>See you on {$domain_name}!</p>

