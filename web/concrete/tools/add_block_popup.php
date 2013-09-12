<?

defined('C5_EXECUTE') or die("Access Denied.");

if (!Loader::helper('validation/numbers')->integer($_REQUEST['cID'])) {
	die(t('Access Denied'));
}

if (!Loader::helper('validation/numbers')->integer($_REQUEST['btID'])) {
	die(t('Access Denied'));
}

$c = Page::getByID($_REQUEST['cID']);
$cp = new Permissions($c);
$bt = BlockType::getByID($_REQUEST['btID']);
$a = Area::get($c, $_REQUEST['arHandle']);
if (!is_object($a)) {
	exit;
}
$ap = new Permissions($a);
$canContinue = ($_REQUEST['btask'] == 'alias') ? $ap->canAddBlocks() : $ap->canAddBlock($bt);

if (!$canContinue) {
	print t('Access Denied');
	exit;
}
	
$c->loadVersionObject('RECENT');
require_once(DIR_FILES_ELEMENTS_CORE . '/dialog_header.php');

if ($ap->canAddBlock($bt)) {
	$cnt = $bt->getController();
	if (!is_a($cnt, 'BlockController')) {
		$jsh = Loader::helper('concrete/interface');
		print '<div class="ccm-error">' . t('Unable to load the controller for this block type. Perhaps it has been moved or removed.') . '</div>';
		print '<br><br>';
		print $jsh->button_js(t('Close'), 'jQuery.fn.dialog.closeTop()', 'left');
	} else {
		$bv = new BlockView($bt);
		// Handle special posted area parameters here
		if (isset($_REQUEST['arGridColumnSpan'])) {
			$a->setAreaGridColumnSpan(intval($_REQUEST['arGridColumnSpan']));
		}
		$bv->addScopeItem('a', $a);
		$bv->addScopeItem('cp', $cp);
		$bv->addScopeItem('ap', $ap);
		
		$bv->render('add');
	}
}

require_once(DIR_FILES_ELEMENTS_CORE . '/dialog_footer.php'); ?>