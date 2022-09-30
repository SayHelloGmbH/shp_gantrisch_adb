<?php

namespace SayHello\ShpGantrischAdb\Package;

/**
 * Rewrite controls for Angebotsdatenbank
 * This allows the page URL to be called with the additional parameter
 * .../offer-id/123456/, the ID of which will then be stored as a
 * validated request variable. This variable can then be used elsewhere,
 * e.g. in a custom Gutenberg block which will be placed in the page.
 *
 * The rewrite endpoint is only registered for use on PAGES not POSTS.
 *
 * @author Say Hello GmbH <hello@sayhello.ch>
 */
class Rewrites
{

	private $var_key = 'offer-id';

	public function run()
	{
		add_action('init', [$this, 'rewriteEndpoint']);
		add_action('request', [$this, 'endpointVars']);
		add_filter('query_vars', [$this, 'extraQueryVars']);
	}

	public function getVarKey()
	{
		return $this->var_key;
	}

	public function rewriteEndpoint()
	{
		add_rewrite_endpoint($this->var_key, EP_PAGES);
	}

	public function endpointVars($vars)
	{

		$offer_id = preg_replace('/[^0-9]/', '', trim($vars[$this->var_key] ?? ''));

		if (!empty($offer_id)) {
			$vars['adb_offer_id'] = $offer_id;
		}

		return $vars;
	}

	public function extraQueryVars($vars)
	{
		$vars[] = 'adb_offer_id';
		return $vars;
	}
}
