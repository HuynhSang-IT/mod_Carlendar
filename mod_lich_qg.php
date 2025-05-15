<?php
defined('_JEXEC') or die;

require_once __DIR__ . '/helper.php';

$lunarData = ModLichQGHelper::getCalendarData();

require JModuleHelper::getLayoutPath('mod_lich_qg', $params->get('layout', 'default'));
