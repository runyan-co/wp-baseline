<?php

/**
 * Plugin Name:     Baseline
 * Description:     A few basic improvements for WordPress. Takes advantage of the @package Soil by Roots
 * Author:          Alex Runyan <alex@runyan.co>
 * Text Domain:     baseline
 * Domain Path:     /languages
 * Version:         0.5.0
 * License:         MIT
 * @package         baseline
 */

/**
 * Copyright 2020 Alex Runyan <alex@runyan.co>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit
 * persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the
 * Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
 * OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

try {

	require 'src/Baseline.php';

	$baseline = new \RunyanCo\Baseline(__DIR__);
	$baseline->initialize();

} catch (\Exception $exception) {
	// @todo Log or report Exceptions here
}
