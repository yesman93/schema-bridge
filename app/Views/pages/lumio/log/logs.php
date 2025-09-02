<?php
/**
 *
 *
 */

use Lumio\View\Components;
use Lumio\Log\Logger;






$logviewer = Components\LogViewer::build($this->logs ?? []);

$logviewer->channels($this->channels ?? []);
$logviewer->channel($this->channel ?? '');

$logviewer->render();









