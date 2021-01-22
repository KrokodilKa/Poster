<?php
namespace VK\Exceptions\Api;

use VK\Client\VKApiError;
use VK\Exceptions\VKApiException;

/**
 */
class VKApiAppAuthException extends VKApiException {

	/**
	 * VKApiAppAuthException constructor.
	 *
	 * @param VkApiError $error
	 */
	public function __construct(VkApiError $error) {
		parent::__construct(28, 'Application authorization failed', $error);
	}
}
