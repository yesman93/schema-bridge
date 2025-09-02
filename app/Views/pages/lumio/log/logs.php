<?php
/**
 *
 * @author TB
 * @date 11.5.2025
 *
 */

use Lumio\View\Components;
use Lumio\Log\Logger;






$logviewer = Components\LogViewer::build($this->logs ?? []);

$logviewer->channels($this->channels ?? []);
$logviewer->channel($this->channel ?? '');

$logviewer->render();









