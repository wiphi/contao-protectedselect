<?php

/*
 * Contact maintainer Jan Theofel: jan@theofel.de
 *
 * PHP version 5
 * @copyright  Jan Theofel 2011, ETES GmbH 2010
 * @author     Jan Theofel <jan@theofel.de>, Andreas Schempp <andreas@schempp.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 * @version    $Id: $
 */
 

/**
 * Run in a custom namespace, so the class can be replaced
 */
namespace Contao;


class ProtectedOptionWizard extends \OptionWizard
{
	/**
	 * Validate input and set value
	 */
	public function validate()
	{
		$arrReferences = array();
		$mandatory = $this->mandatory;
		$options = deserialize($this->getPost($this->strName));

		// Check labels only (values can be empty)
		if (is_array($options))
		{
			foreach ($options as $key=>$option)
			{
				$options[$key]['label'] = trim($option['label']);
				$options[$key]['value'] = trim($option['value']);
				$options[$key]['reference'] = trim($option['reference']);

				if (strlen($options[$key]['label']))
				{
					$this->mandatory = false;
				}
				
				$arrReferences[] = $options[$key]['reference'];
			}
		}
		
		if (count(array_unique($arrReferences)) != count($arrReferences))
		{
			$this->addError($GLOBALS['TL_LANG']['ERR']['uniqueReference']);
		}

		$options = array_values($options);
		$varInput = $this->validator($options);

		if (!$this->hasErrors())
		{
			$this->varValue = $varInput;
		}

		// Reset the property
		if ($mandatory)
		{
			$this->mandatory = true;
		}
	}


	/**
	 * Generate the widget and return it as string
	 * @return string
	 */
	public function generate()
	{
		$arrButtons = array('copy', 'up', 'down', 'delete');
		$strCommand = 'cmd_' . $this->strField;

		// Change the order
		if (\Input::get($strCommand) && is_numeric(\Input::get('cid')) && \Input::get('id') == $this->currentRecord)
		{
			$this->import('Database');

			switch (\Input::get($strCommand))
			{
				case 'copy':
					array_insert($this->varValue, \Input::get('cid'), array($this->varValue[\Input::get('cid')]));
					break;

				case 'up':
					$this->varValue = array_move_up($this->varValue, \Input::get('cid'));
					break;

				case 'down':
					$this->varValue = array_move_down($this->varValue, \Input::get('cid'));
					break;

				case 'delete':
					$this->varValue = array_delete($this->varValue, \Input::get('cid'));
					break;
			}

			$this->Database->prepare("UPDATE " . $this->strTable . " SET " . $this->strField . "=? WHERE id=?")
						   ->execute(serialize($this->varValue), $this->currentRecord);

			$this->redirect(preg_replace('/&(amp;)?cid=[^&]*/i', '', preg_replace('/&(amp;)?' . preg_quote($strCommand, '/') . '=[^&]*/i', '', $this->Environment->request)));
		}

		// Make sure there is at least an empty array
		if (!is_array($this->varValue) || !$this->varValue[0])
		{
			$this->varValue = array(array(''));
		}

		// Begin table
		$return .= '<table cellspacing="0" cellpadding="0" class="tl_optionwizard" id="ctrl_'.$this->strId.'" summary="Field wizard">
  <thead>
    <tr>
      <th>'.$GLOBALS['TL_LANG'][$this->strTable]['opReference'].'</th>
      <th>'.$GLOBALS['TL_LANG'][$this->strTable]['opValueProtected'].'</th>
      <th>'.$GLOBALS['TL_LANG'][$this->strTable]['opLabel'].'</th>
      <th>&nbsp;</th>
      <th>&nbsp;</th>
      <th>&nbsp;</th>
    </tr>
  </thead>
  <tbody>';

		// Add fields
		for ($i=0; $i<count($this->varValue); $i++)
		{
			$return .= '
    <tr>
      <td><input type="text" name="'.$this->strId.'['.$i.'][reference]" id="'.$this->strId.'_reference_'.$i.'" class="tl_text_4" value="'.specialchars($this->varValue[$i]['reference']).'" /></td>
      <td><input type="text" name="'.$this->strId.'['.$i.'][value]" id="'.$this->strId.'_value_'.$i.'" class="tl_text_2" value="'.specialchars($this->varValue[$i]['value']).'" /></td>
      <td><input type="text" name="'.$this->strId.'['.$i.'][label]" id="'.$this->strId.'_label_'.$i.'" class="tl_text_2" value="'.specialchars($this->varValue[$i]['label']).'" /></td>
      <td><input type="checkbox" name="'.$this->strId.'['.$i.'][default]" id="'.$this->strId.'_default_'.$i.'" class="fw_checkbox" value="1"'.($this->varValue[$i]['default'] ? ' checked="checked"' : '').' /> <label for="'.$this->strId.'_default_'.$i.'">'.$GLOBALS['TL_LANG'][$this->strTable]['opDefault'].'</label></td>
      <td><input type="checkbox" name="'.$this->strId.'['.$i.'][group]" id="'.$this->strId.'_group_'.$i.'" class="fw_checkbox" value="1"'.($this->varValue[$i]['group'] ? ' checked="checked"' : '').' /> <label for="'.$this->strId.'_group_'.$i.'">'.$GLOBALS['TL_LANG'][$this->strTable]['opGroup'].'</label></td>';
			
			// Add row buttons
			$return .= '
      <td style="white-space:nowrap; padding-left:3px;">';

			foreach ($arrButtons as $button)
			{
				$return .= '<a href="'.$this->addToUrl('&amp;'.$strCommand.'='.$button.'&amp;cid='.$i.'&amp;id='.$this->currentRecord).'" title="'.specialchars($GLOBALS['TL_LANG'][$this->strTable][$button][0]).'" onclick="Backend.optionsWizard(this, \''.$button.'\', \'ctrl_'.$this->strId.'\'); return false;">'.$this->generateImage($button.'.gif', $GLOBALS['TL_LANG'][$this->strTable][$button][0]).'</a> ';
			}

			$return .= '</td>
    </tr>';
		}

		return $return.'
  </tbody>
  </table>';
	}
}
