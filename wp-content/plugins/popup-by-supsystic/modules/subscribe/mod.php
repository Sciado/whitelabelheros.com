<?php
class subscribePps extends modulePps {
	private $_destList = array();
	public function getDestList() {
		if(empty($this->_destList)) {
			$this->_destList = array(
				'wordpress' => array('label' => __('WordPress', PPS_LANG_CODE), 'require_confirm' => true),
				'aweber' => array('label' => __('Aweber', PPS_LANG_CODE)),
				'mailchimp' => array('label' => __('MailChimp', PPS_LANG_CODE), 'require_confirm' => true),
				'mailpoet' => array('label' => __('MailPoet', PPS_LANG_CODE), 'require_confirm' => true),
			);
		}
		return $this->_destList;
	}
	public function getDestByKey($key) {
		$this->getDestList();
		return isset($this->_destList[ $key ]) ? $this->_destList[ $key ] : false;
	}
	public function generateFormStart($popup) {
		$res = '';
		if(isset($popup['params']['tpl']['sub_dest']) && !empty($popup['params']['tpl']['sub_dest'])) {
			$subDest = $popup['params']['tpl']['sub_dest'];
			$view = $this->getView();
			$generateMethod = 'generateFormStart_'. $subDest;
			if(method_exists($view, $generateMethod)) {
				$res = $view->$generateMethod( $popup );
			}
			$res = dispatcherPps::applyFilters('subFormStart', $res, $popup);
		}
		return $res;
	}
	public function generateFormEnd($popup) {
		$res = '';
		if(isset($popup['params']['tpl']['sub_dest']) && !empty($popup['params']['tpl']['sub_dest'])) {
			$subDest = $popup['params']['tpl']['sub_dest'];
			$view = $this->getView();
			$generateMethod = 'generateFormEnd_'. $subDest;
			if(method_exists($view, $generateMethod)) {
				$res = $view->$generateMethod( $popup );
			}
			$res = dispatcherPps::applyFilters('subFormEnd', $res, $popup);
		}
		return $res;
	}
	public function loadAdminEditAssets() {
		framePps::_()->addScript('admin.subscribe', $this->getModPath(). 'js/admin.subscribe.js');
	}
	public function getAvailableUserRolesForSelect() {
		global $wp_roles;
		$res = array();
		$allRoles = $wp_roles->roles;
		$editableRoles = apply_filters('editable_roles', $allRoles);
		
		if(!empty($editableRoles)) {
			foreach($editableRoles as $role => $data) {
				if(in_array($role, array('administrator', 'editor'))) continue;
				if($role == 'subscriber') {	// Subscriber - at the begining of array
					$res = array($role => $data['name']) + $res;
				} else {
					$res[ $role ] = $data['name'];
				}
			}
		}
		return $res;
	}
	public function generateFields($popup) {
		$resHtml = '';
		foreach($popup['params']['tpl']['sub_fields'] as $k => $f) {
			if(isset($f['enb']) && $f['enb']) {
				$htmlType = $f['html'];
				$htmlParams = array(
					'placeholder' => $f['label'],
				);
				if($htmlType == 'selectbox' && isset($f['options']) && !empty($f['options'])) {
					$htmlParams['options'] = array();
					foreach($f['options'] as $opt) {
						$htmlParams['options'][ $opt['name'] ] = $opt['label'];
					}
				}
				$inputHtml = htmlPps::$htmlType($k, $htmlParams);
				if($htmlType == 'selectbox') {
					$inputHtml = '<label class="ppsSubSelect"><span class="ppsSubSelectLabel">'. $f['label']. ': </span>'. $inputHtml. '</label>';
				}
				$resHtml .= $inputHtml;
			}
		}
		return $resHtml;
	}
}

