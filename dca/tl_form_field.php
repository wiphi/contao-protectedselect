<?php

/*
 * protected select
 * Adds a new formula select widget which hides the internal field values in the frontend
 *
 * @copyright  Christian Barkowsky 2015-2017, Jan Theofel 2011-2014, ETES GmbH 2010
 * @author     Christian Barkowsky <hallo@christianbarkowsky.de>
 * @author     Jan Theofel <jan@theofel.de>
 * @author     Andreas Schempp <andreas@schempp.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */


/**
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_form_field']['palettes']['protectedselect'] = '{type_legend},type,name,label;{options_legend},protectedOptions;{fconfig_legend},mandatory,multiple;{expert_legend:hide},class,accesskey,tabindex;{template_legend:hide},customTpl;{submit_legend},addSubmit';


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_form_field']['fields']['protectedOptions'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_form_field']['protectedOptions'],
    'exclude'                 => true,
    'inputType'               => 'protectedOptionWizard',
    'eval'                    => array('mandatory'=>true),
    'xlabel' => array
    (
        array('tl_form_field_protectedoptions', 'protectedOptionImportWizard')        
    ),
    'sql'                     => "blob NULL",
);

class tl_form_field_protectedoptions extends tl_form_field
{
	/**
	 * Add a link to the option items import wizard
	 *
	 * @return string
	 */
	public function protectedOptionImportWizard()
	{
		return ' <a href="' . $this->addToUrl('key=protectedoption') . '" title="' . specialchars($GLOBALS['TL_LANG']['MSC']['ow_import'][1]) . '" onclick="Backend.getScrollOffset()">' . Contao\Image::getHtml('tablewizard.svg', $GLOBALS['TL_LANG']['MSC']['ow_import'][0]) . '</a>';
	}

}