<?php

namespace RunyanCo\WpBaseline;

trait Utilities
{

	/**
	 * @var array
	 */
	public $debugItems = [];

	/**
	 * @var string
	 */
	public $noticeType;

	/**
	 * @var string
	 */
	public $noticeMessage;

	/**
	 * Debug tool
	 *
	 * @param  array  $debugItems
	 *
	 * @return void
	 */
	public function printToBrowserConsole(...$debugItems)
	{
		$this->debugItems = $debugItems;

		add_action('admin_head', function () {
			foreach ($this->debugItems as $printedAsString) {
				try { print '<script> console.log(\''. ($printedAsString ?? 'null') .'\') </script>'; }
				catch (\Exception $exception) {}
			}
		});
	}

	/**
	 * Add simple admin notices when plugin is activated and deactivated
	 *
	 * @param  string  $type
	 * @param  string  $message
	 *
	 * @return void
	 */
	public function addAdminNotice(string $type, string $message)
	{
		$this->noticeType = $type;
		$this->noticeMessage = $message;

		add_action('admin_notices', function () {
			$this->formatAdminNotice();
		});
	}

	/**
	 * @param  string  $string
	 *
	 * @return string|string[]|null
	 */
	public function titleCase(string $string)
	{
		return preg_replace('/(?<!\ )[A-Z]/', ' $0', $string);
	}

	/**
	 * Returns the formatted string (the markup of) the admin notice.
	 * Types include: error, info, warning, success
	 *
	 * @return int
	 */
	public function formatAdminNotice()
	{
		if (isset($this->noticeType) && isset($this->noticeMessage)) {

			if (! array_search($this->noticeType, ['error', 'info', 'warning', 'success'])) {
				$this->noticeType = 'info';
			}

			return printf(
				'<div class="%1$s"><p>%2$s</p></div>',
				esc_attr('notice is-dismissible notice-'. $this->noticeType),
				esc_html(__($this->noticeMessage, WP_BASELINE_TEXT_DOMAIN))
			);
		}

		return printf('');
	}
}
